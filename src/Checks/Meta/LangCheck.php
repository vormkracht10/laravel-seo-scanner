<?php

namespace Vormkracht10\Seo\Checks\Meta;

use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;
use Symfony\Component\DomCrawler\Crawler;

class LangCheck implements Check
{
    use PerformCheck;

    public string $title = 'The lang attribute is set on the html tag';

    public string $priority = 'medium';

    public int $timeToFix = 1;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = true;

    public function check(Response $response): bool
    {
        if (! $this->validateContent($response)) {
            return false;
        }

        return true;
    }

    public function validateContent(Response $response): bool
    {
        $response = $response->body();

        $crawler = new Crawler($response);

        $lang = $crawler->filter('html')->attr('lang');

        if (! $lang) {
            return false;
        }

        return true;
    }
}
