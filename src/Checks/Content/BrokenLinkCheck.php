<?php

namespace Vormkracht10\Seo\Checks\Content;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class BrokenLinkCheck implements Check
{
    use PerformCheck;

    public string $title = 'The page contains no broken links';

    public string $priority = 'medium';

    public int $timeToFix = 10;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = true;

    public string|null $failureReason;

    public mixed $actualValue = null;

    public mixed $expectedValue = null;

    public function check(Response $response, Crawler $crawler): bool
    {
        if (! $this->validateContent($crawler)) {
            return false;
        }

        return true;
    }

    public function validateContent(Crawler $crawler): bool
    {
        $content = $crawler->filterXPath('//a')->each(function (Crawler $node, $i) {
            return $node->attr('href');
        });

        if (! $content) {
            return true;
        }

        $content = collect($content)->filter(fn ($value) => $value !== null)
            ->map(fn ($link) => addBaseIfRelativeUrl($link, $this->url))
            ->filter(function ($link) {
                // Filter out all links that are mailto, tel or have a file extension
                if (preg_match('/^mailto:/msi', $link) ||
                    preg_match('/^tel:/msi', $link) ||
                    preg_match('/\.[a-z]{2,4}$/msi', $link) ||
                    filter_var($link, FILTER_VALIDATE_URL) === false
                ) {
                    return false;
                }

                return $link;
            })
            ->filter(fn ($link) => isBrokenLink($link))->toArray();

        dd($content);

        $this->actualValue = $content;

        if (count($content) > 0) {
            dd($content);
            $this->failureReason = __('failed.content.broken_links', [
                'actualValue' => implode(', ', $content),
            ]);

            return false;
        }

        return true;
    }
}
