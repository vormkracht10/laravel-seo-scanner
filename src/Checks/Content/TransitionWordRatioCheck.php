<?php

namespace Vormkracht10\Seo\Checks\Content;

use Readability\Readability;
use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\Check;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Traits\PerformCheck;
use Vormkracht10\Seo\Helpers\TransitionWords;

class TransitionWordRatioCheck implements Check
{
    use PerformCheck;

    public string $title = 'Transition word ratio check';

    public string $priority = 'medium';

    public int $timeToFix = 60;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = true;

    public ?string $failureReason;

    public mixed $actualValue = null;

    public mixed $expectedValue = null;

    public function check(Response $response, Crawler $crawler): bool
    {
        if (! $this->validateContent($response, $crawler)) {
            return false;
        }

        return true;
    }

    public function validateContent(Response $response, Crawler $crawler): bool
    {
        $content = $crawler->filterXPath('//body')->text();

        $readability = new Readability($response->body());
        $readability->init();

        $transitionWords = TransitionWords::getTransitionWordsOnly(config('seo.language'));

        $this->actualValue = $this->calculatePercentageOfTransitionWordsInContent($content, $transitionWords);

        if ($this->actualValue < 30) {
            $this->failureReason = __('failed.content.transition_words_ratio_check.too_few_transition_words', [
                'actualValue' => $this->actualValue,
            ]);

            return false;
        }

        return true;
    }

    public function calculatePercentageOfTransitionWordsInContent($content, $transitionWords)
    {
        $totalPhrases = preg_match_all('/\b[\w\s]+\b/', $content, $matches);

        if ($totalPhrases === 0) {
            $this->actualValue = 0;
            $this->failureReason = __('failed.content.transition_words_ratio_check.no_phrases_found');

            return 0;
        }

        $phrasesWithTransitionWord = 0;

        foreach ($transitionWords as $transitionWord) {
            $phrasesWithTransitionWord += $this->calculateNumberOfPhrasesWithTransitionWord($content, $transitionWord);
        }

        return round($phrasesWithTransitionWord / $totalPhrases * 100, 0, PHP_ROUND_HALF_UP);
    }

    public function calculateNumberOfPhrasesWithTransitionWord(string $content, string $transitionWord): int
    {
        preg_match_all('/\b[\w\s]+\b/', $content, $matches);

        $phrasesWithTransitionWord = 0;

        foreach ($matches[0] as $phrase) {
            if (stripos($phrase, $transitionWord) !== false) {
                $phrasesWithTransitionWord++;
            }
        }

        return $phrasesWithTransitionWord;
    }
}