<?php

namespace Vormkracht10\Seo\Checks\Content;

use Illuminate\Support\Str;
use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\Check;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Traits\PerformCheck;
use Vormkracht10\Seo\Checks\Helpers\TransitionWords;

class TransitionWordRatioCheck implements Check
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
            // $this->failureReason = __('failed.meta.keyword_in_title_check');

            return false;
        }

        return true;
    }

    public function validateContent(Crawler $crawler): bool
    {
        // Get the content of the page
        $content = $crawler->filter('body')->text();

        // Get the transition words
        // TODO: Get transition words by language. For now, we only support English.
        // TODO: Add Dutch as well.
        $transitionWords = TransitionWords::getTransitionWordsOnly();

        $this->actualValue = $this->calculatePercentageOfTransitionWordsInContent($content, $transitionWords);
    }

    public function calculatePercentageOfTransitionWordsInContent(string $content, array $transitionWords): int
    {
        $totalWords = str_word_count($content);

        $transitionWordsInContent = 0;

        foreach ($transitionWords as $transitionWord) {
            $transitionWordsInContent += substr_count($content, $transitionWord);
        }

        return $transitionWordsInContent / $totalWords * 100;
    }
}
