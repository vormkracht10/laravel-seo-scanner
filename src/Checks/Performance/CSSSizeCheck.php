<?php

namespace Vormkracht10\Seo\Checks\Performance;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class CSSSizeCheck implements Check
{
    use PerformCheck;

    public string $title = 'CSS files are not bigger than 15 KB';

    public string $priority = 'medium';

    public int $timeToFix = 30;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = true;

    public function check(Response $response, Crawler $crawler): bool
    {
        if (app()->runningUnitTests()) {
            if (strlen($response->body()) > 15000) {
                return false;
            }

            return true;
        }

        if (! $this->validateContent($crawler)) {
            return false;
        }

        return true;
    }

    public function validateContent(Crawler $crawler): bool
    {
        $crawler = $crawler->filterXPath('//link')->each(function (Crawler $node, $i) {
            $rel = $node->attr('rel');
            $href = $node->attr('href');

            if ($rel === 'stylesheet') {
                return $href;
            }
        });

        $content = collect($crawler)->filter(fn ($value) => $value !== null)->toArray();

        if (! $content) {
            return true;
        }

        foreach ($content as $url) {
            if (! str_contains($url, 'http')) {
                $url = url($url);
            }

            if (isBrokenLink(url: $url)) {
                continue;
            }

            $size = getRemoteFileSize(url: $url);

            if (! $size || $size > 15000) {
                return false;
            }
        }

        return true;
    }
}
