<?php

namespace Vormkracht10\Seo\Checks\Meta;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class LangCheck implements Check
{
    use PerformCheck;

    public string $title = 'The lang attribute is set on the html tag';

    public string $priority = 'medium';

    public int $timeToFix = 1;

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
        $node = $crawler->filterXPath('//html')->getNode(0);

        if (! $node) {
            $this->failureReason = __('failed.meta.no_lang');

            return false;
        }

        $lang = $crawler->filterXPath('//html')->attr('lang');

        if (! $lang) {
            $this->failureReason = __('failed.meta.no_lang');

            return false;
        }

        return true;
    }
}
