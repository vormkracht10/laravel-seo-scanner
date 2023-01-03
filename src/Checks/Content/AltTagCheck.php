<?php

namespace Vormkracht10\Seo\Checks\Content;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class AltTagCheck implements Check
{
    use PerformCheck;

    public string $title = 'Every image has an alt tag';

    public string $priority = 'low';

    public int $timeToFix = 5;

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
        $imagesWithoutAlt = $crawler->filterXPath('//img[not(@alt)]')->each(function (Crawler $node, $i) {
            return false;
        });

        $imagesWithEmptyAlt = $crawler->filterXPath('//img[@alt=""]')->each(function (Crawler $node, $i) {
            return false;
        });

        $imagesWithoutAlt = array_merge($imagesWithoutAlt, $imagesWithEmptyAlt);

        return collect($imagesWithoutAlt)->first(fn ($value) => $value === false) ?? true;
    }
}
