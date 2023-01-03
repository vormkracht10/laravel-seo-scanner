<?php

namespace Vormkracht10\Seo\Checks\Performance;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class HTMLSizeCheck implements Check
{
    use PerformCheck;

    public string $title = 'HTML is not larger than 100 KB';

    public string $priority = 'medium';

    public int $timeToFix = 60;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = true;

    public function check(Response $response, Crawler $crawler): bool
    {
        if ($this->validateContent($response)) {
            return false;
        }

        return true;
    }

    public function validateContent(Response $response): bool
    {
        $content = $response->body();

        if (! $content) {
            return false;
        }

        return strlen($content) < 100000;
    }
}
