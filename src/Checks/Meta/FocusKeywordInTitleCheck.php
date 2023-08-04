<?php

namespace Vormkracht10\Seo\Checks\Meta;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class FocusKeywordInTitleCheck implements Check
{
    use PerformCheck;

    public string $title = 'The page has the focus keyword in the title';

    public string $priority = 'medium';

    public int $timeToFix = 1;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = true;

    public ?string $failureReason;

    public mixed $actualValue = null;

    public mixed $expectedValue = null;

    public function check(Response $response, Crawler $crawler): bool
    {
        if (! $this->validateContent($crawler)) {
            $this->failureReason = 'test';
            // $this->failureReason = __('failed.meta.focus_keyword_in_title_check');

            return false;
        }

        return true;
    }

    public function validateContent(Crawler $crawler): bool
    {
        $keywords = $this->getKeywords($crawler);
        $node = $crawler->filterXPath('//meta[@name="description"]')->getNode(0);

        if (! $node) {
            return false;
        }

        $content = $crawler->filterXPath('//meta[@name="description"]')->attr('content');

        dd($content, $keywords);

        // if (! $content) {
        //     return false;
        // }

        return true;
    }

    public function getKeywords(Crawler $crawler): array
    {
        $node = $crawler->filterXPath('//meta[@name="keywords"]')->getNode(0);

        if (! $node) {
            return [];
        }

        $keywords = $crawler->filterXPath('//meta[@name="keywords"]')->attr('content');

        if (! $keywords) {
            return [];
        }

        return explode(', ', $keywords);
    }
}
