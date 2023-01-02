<?php

namespace Vormkracht10\Seo\Checks\Content;

use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\Check;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Traits\PerformCheck;

class AltTagCheck implements Check
{
    use PerformCheck;

    public string $title = 'Every image has an alt tag';

    public string $priority = 'low';

    public int $timeToFix = 5;

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

        $imagesWithoutAlt = $crawler->filter('img')->each(function (Crawler $node, $i) {
            if (! $node->attr('alt')) {
                return false;
            }

            return true;
        });

        return collect($imagesWithoutAlt)->first(fn ($value) => $value === false) ?? true;
    }
}
