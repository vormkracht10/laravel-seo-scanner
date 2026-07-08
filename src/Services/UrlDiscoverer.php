<?php

namespace Backstage\Seo\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class UrlDiscoverer
{
    /**
     * File extensions that never point to scannable HTML pages.
     */
    private const EXCLUDED_EXTENSIONS = [
        'png', 'jpg', 'jpeg', 'gif', 'webp', 'avif', 'svg', 'ico',
        'css', 'js', 'json', 'xml', 'txt', 'pdf', 'zip', 'gz',
        'mp3', 'mp4', 'webm', 'woff', 'woff2', 'ttf', 'eot',
    ];

    /**
     * Discover the scannable page URLs of an external domain.
     *
     * URLs are taken from the domain's XML sitemap(s) when available (found
     * via robots.txt or the /sitemap.xml convention) and otherwise gathered
     * by a bounded same-host crawl starting at the homepage.
     *
     * @return Collection<int, string>
     */
    public function discover(string $domain, ?int $maxPages = null): Collection
    {
        $baseUrl = $this->normalizeBaseUrl($domain);
        $maxPages = $maxPages ?? (int) config('seo.crawl.max_pages', 50);

        $urls = $this->discoverFromSitemaps($baseUrl, $maxPages);

        if ($urls->isEmpty()) {
            $urls = $this->discoverByCrawling($baseUrl, $maxPages);
        }

        return $urls->when($urls->isEmpty(), fn () => collect([$baseUrl]))
            ->unique()
            ->take($maxPages)
            ->values();
    }

    /**
     * Turn user input like "example.com" or "https://example.com/path" into
     * a scheme-qualified base URL without path, query or fragment.
     */
    public function normalizeBaseUrl(string $domain): string
    {
        $domain = trim($domain);

        if (! preg_match('#^https?://#i', $domain)) {
            $domain = 'https://'.$domain;
        }

        $parts = parse_url($domain);

        if (empty($parts['host'])) {
            throw new \InvalidArgumentException("Could not determine a host from `{$domain}`.");
        }

        $scheme = $parts['scheme'] ?? 'https';

        return $scheme.'://'.$parts['host'].(isset($parts['port']) ? ':'.$parts['port'] : '');
    }

    /**
     * @return Collection<int, string>
     */
    private function discoverFromSitemaps(string $baseUrl, int $maxPages): Collection
    {
        $queue = $this->sitemapUrlsFromRobots($baseUrl) ?: [$baseUrl.'/sitemap.xml'];
        $urls = collect();
        $fetched = 0;

        while (! empty($queue) && $urls->count() < $maxPages && $fetched < 50) {
            $sitemapUrl = array_shift($queue);
            $fetched++;

            $response = $this->fetch($sitemapUrl);

            if (! $response?->successful()) {
                continue;
            }

            $body = (string) $response->body();
            $locations = $this->extractLocations($body);

            if (preg_match('/<\s*sitemapindex[\s>]/i', $body)) {
                $queue = array_merge($queue, $locations);

                continue;
            }

            $urls = $urls->merge(
                collect($locations)->filter(fn (string $url) => $this->isScannableUrl($url, $baseUrl))
            );
        }

        return $urls;
    }

    /**
     * Breadth-first crawl of same-host links, used when no sitemap exists.
     *
     * @return Collection<int, string>
     */
    private function discoverByCrawling(string $baseUrl, int $maxPages): Collection
    {
        $maxDepth = (int) config('seo.crawl.max_depth', 2);

        $queue = [[$baseUrl.'/', 0]];
        $seen = [$baseUrl.'/' => true, $baseUrl => true];
        $urls = collect();

        while (! empty($queue) && $urls->count() < $maxPages) {
            [$url, $depth] = array_shift($queue);

            $response = $this->fetch($url);

            if (! $response?->successful() || ! Str::contains(strtolower($response->header('Content-Type')), 'html')) {
                continue;
            }

            $urls->push($url);

            if ($depth >= $maxDepth) {
                continue;
            }

            foreach ($this->extractLinks($response->body(), $url) as $link) {
                if (isset($seen[$link]) || ! $this->isScannableUrl($link, $baseUrl)) {
                    continue;
                }

                $seen[$link] = true;
                $queue[] = [$link, $depth + 1];
            }
        }

        return $urls;
    }

    /**
     * @return array<int, string>
     */
    private function sitemapUrlsFromRobots(string $baseUrl): array
    {
        $response = $this->fetch($baseUrl.'/robots.txt');

        if (! $response?->successful()) {
            return [];
        }

        $sitemaps = [];

        foreach (preg_split('/\r\n|\r|\n/', (string) $response->body()) as $line) {
            if (preg_match('/^\s*sitemap\s*:\s*(\S+)/i', $line, $matches)) {
                $sitemaps[] = trim($matches[1]);
            }
        }

        return $sitemaps;
    }

    /**
     * @return array<int, string>
     */
    private function extractLocations(string $xml): array
    {
        preg_match_all('/<\s*loc\s*>\s*(.*?)\s*<\s*\/\s*loc\s*>/is', $xml, $matches);

        return array_map(fn (string $loc) => html_entity_decode($loc), $matches[1]);
    }

    /**
     * @return array<int, string>
     */
    private function extractLinks(string $html, string $pageUrl): array
    {
        $links = [];

        foreach ((new Crawler($html, $pageUrl))->filter('a[href]') as $node) {
            $href = trim((string) $node->getAttribute('href'));

            if ($href === '' || Str::startsWith($href, ['#', 'mailto:', 'tel:', 'javascript:'])) {
                continue;
            }

            $links[] = Str::before(addBaseIfRelativeUrl($href, $pageUrl), '#');
        }

        return array_unique($links);
    }

    private function isScannableUrl(string $url, string $baseUrl): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        $baseHost = parse_url($baseUrl, PHP_URL_HOST);

        if (! $host || ! $this->isSameHost($host, $baseHost)) {
            return false;
        }

        $extension = strtolower(pathinfo((string) parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

        return $extension === '' || ! in_array($extension, self::EXCLUDED_EXTENSIONS);
    }

    /**
     * Treat the www subdomain and the apex domain as the same host, since
     * sitemaps regularly reference the canonical variant of the two.
     */
    private function isSameHost(string $host, string $baseHost): bool
    {
        $strip = fn (string $value) => preg_replace('/^www\./', '', strtolower($value));

        return $strip($host) === $strip($baseHost);
    }

    private function fetch(string $url): ?Response
    {
        try {
            return Http::withOptions((array) config('seo.http.options', []))
                ->withHeaders((array) config('seo.http.headers', []))
                ->timeout(30)
                ->get($url);
        } catch (\Throwable) {
            return null;
        }
    }
}
