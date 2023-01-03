<?php

namespace Vormkracht10\Seo\Checks\Content;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class BrokenImageCheck implements Check
{
    use PerformCheck;

    public string $title = 'The page contains no broken images';

    public string $priority = 'medium';

    public int $timeToFix = 10;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = true;

    public function check(Response $response, Crawler $crawler): bool
    {
        if (! $this->validateContent($crawler)) {
            return false;
        }

        return true;
    }

    public function validateContent(Crawler $crawler): bool
    {
        $content = $crawler->filterXPath('//img')->each(function (Crawler $node, $i) {
            return $node->attr('src');
        });

        if (! $content) {
            return true;
        }

        $content = collect($content)->filter(fn ($value) => $value !== null)
            ->filter(fn ($link) => isBrokenLink($link));

        return count($content) === 0;
    }
}
