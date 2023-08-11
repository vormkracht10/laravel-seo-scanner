<?php

namespace Vormkracht10\Seo\Checks\Content;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class TooLongSentenceCheck implements Check
{
    use PerformCheck;

    public string $title = 'Too long sentence check';

    public string $priority = 'medium';

    public int $timeToFix = 45;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = true;

    public ?string $failureReason;

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
        $realSentences = [];
        $sentences = $this->getSentencesFromCrawler($crawler);

        $sentences = $this->separateSentencesByDot($sentences);

        $sentencesWithTooManyWords = $this->calculateSentencesWithTooManyWords($sentences);

        $this->actualValue = $this->calculateSentencesWithTooManyWords($sentences);

        if (count($sentencesWithTooManyWords) === 0) {
            return true;
        }

        // If more than 20% of the total sentences are too long, fail
        if (count($sentencesWithTooManyWords) / count($sentences) > 0.2) {
            $this->failureReason = __('failed.content.too_long_sentence', [
                'actualValue' => count($this->actualValue),
            ]);

            return false;
        }

        return true;
    }

    private function separateSentencesByDot(array $sentences): array
    {
        $newSentences = [];

        foreach ($sentences as $sentence) {        
            $sentence = explode('.', $sentence);
            $newSentences = array_merge($newSentences, $sentence);
        }

        // Remove all sentences that are empty
        $sentences = array_filter($newSentences, function ($sentence) {
            return ! empty($sentence);
        });

        return $sentences;
    }

    private function getSentencesFromCrawler(Crawler $crawler): array
    {
        $content = $crawler->filterXPath('//body')->children();

        // Get all elements that contain text
        $content = $content->filterXPath('//*/text()[normalize-space()]');

        $content = $content->each(function (Crawler $node, $i) {
            return $node->text();
        });

        return $content;
    }

    private function calculateSentencesWithTooManyWords(array $sentences): array
    {
        $tooLongSentences = [];

        foreach ($sentences as $sentence) {
            if (str_word_count($sentence) > 20) {
                $tooLongSentences[] = $sentence;
            }
        }

        return $tooLongSentences;
    }
}
