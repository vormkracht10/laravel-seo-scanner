<?php

namespace Vormkracht10\Seo\Checks\Meta;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class TitleCheck implements Check
{
    use PerformCheck;

    public string $title = "The page title does not contain 'home' or 'homepage'";

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

        $content = strtolower($content);

        return ! str_contains($content, 'home') && ! str_contains($content, 'homepage');
    }
}
