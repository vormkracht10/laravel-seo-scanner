<?php

namespace Vormkracht10\Seo\Checks\Content;

use Closure;
use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Traits\FormatRequest;
use Vormkracht10\Seo\Interfaces\ContentCheck;

class BrokenLinkCheck implements ContentCheck
{
    use FormatRequest;

    public string $title = 'Check if links are broken';

    public string $priority = 'medium';

    public int $timeToFix = 10;

    public int $scoreWeight = 5;

    public bool $checkSuccessful = false;

    public function handle(array $request, Closure $next): array
    {
        $content = $this->getContent($request[0]);

        if (! $content) {
            $this->checkSuccessful = true;

            return $next($this->formatRequest($request));
        }

        if (! $this->validateContent($content)) {
            return $next($this->formatRequest($request));
        }

        $this->checkSuccessful = true;

        return $next($this->formatRequest($request));
    }

    public function getContent(Response $response): string|array|null
    {
        $response = $response->body();

        preg_match_all('/<a.*?href="(.*?)".*?>/msi', $response, $matches);

        return $matches[0] ?? null;
    }

    public function validateContent(string|array $content): bool
    {
        if (! is_array($content)) {
            $content = [$content];
        }

        $content = collect($content)->map(function ($item) {
            preg_match('/href="(.*?)"/msi', $item, $matches);

            return $matches[1] ?? false;
        })->filter(function ($item) {
            // Filter out all links that are mailto, tel or have a file extension
            if (preg_match('/^mailto:/msi', $item) ||
                preg_match('/^tel:/msi', $item) ||
                preg_match('/\.[a-z]{2,4}$/msi', $item ||
                filter_var($item, FILTER_VALIDATE_URL) === false)
            ) {
                return false;
            }

            return $item ?? false;
        })->toArray();

        $content = array_filter($content, function ($item) {
            return ! $this->isBrokenLink($item);
        });

        return count($content) === 0;
    }

    public function isBrokenLink(string $url): bool
    {
        $ch = curl_init($url);

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_TIMEOUT => 10,
        ];

        curl_setopt_array($ch, $options);
        curl_exec($ch);

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return $statusCode !== 200;
    }
}
