<?php

namespace Vormkracht10\Seo\Checks\Meta;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class TitleLengthCheck implements Check
{
    use PerformCheck;

    public string $title = 'The page title is not longer than 60 characters';

    public string $priority = 'medium';

    public int $timeToFix = 1;

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
        $node = $crawler->filterXPath('//title')->getNode(0);

        if (! $node) {
            return false;
        }

        $content = $crawler->filterXPath('//title')->text();

        if (! $content) {
            return false;
        }

        return strlen($content) <= 60;
    }
}
