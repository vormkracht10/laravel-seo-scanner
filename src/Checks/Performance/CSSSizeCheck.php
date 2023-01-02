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

    public string|null $failureReason;

    public array|int|string|null $actualValue = null;

    public int|null|string $expectedValue = 15000;

    public function check(Response $response): bool
    {
        $this->expectedValue = bytesToHumanReadable($this->expectedValue);
        $content = $this->getContentToValidate($response);

        if (! $content) {
            return true;
        }

        if (! $this->validateContent($content)) {
            return false;
        }

        return true;
    }

    public function getContentToValidate(Response $response): string|array|null
    {
        $response = $response->body();

        $crawler = new Crawler($response);

        $crawler = $crawler->filterXPath('//link')->each(function (Crawler $node, $i) {
            $rel = $node->attr('rel');
            $href = $node->attr('href');

            if ($rel === 'stylesheet') {
                return $href;
            }
        });

        return collect($crawler)->filter(fn ($value) => $value !== null)->toArray();
    }

    public function validateContent(string|array $content): bool
    {
        if (! is_array($content)) {
            $content = [$content];
        }

        $links = [];

        $tooBigLinks = collect($content)->filter(function ($url) use (&$links) {
            if (! str_contains($url, 'http')) {
                $url = url($url);
            }

            if (isBrokenLink(url: $url)) {
                return false;
            }

            $size = getRemoteFileSize(url: $url);

            if (! $size || $size > 15000) {

                $size = $size ? bytesToHumanReadable($size) : 'unknown';

                $links[] = $url . ' (size: ' . $size . ')';

                return true;
            }

            return false;
        })->toArray();

        if ($tooBigLinks) {

            $this->actualValue = $links;

            $this->failureReason = __('failed.performance.css_size', [
                'actualValue' => implode(', ', $links),
                'expectedValue' => $this->expectedValue,
            ]);

            return false;
        }

        return true;
    }
}
