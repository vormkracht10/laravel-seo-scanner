<?php

namespace Vormkracht10\Seo\Checks\Content;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\Actions;
use Vormkracht10\Seo\Traits\PerformCheck;

class TooLongSentenceCheck implements Check
{
    use PerformCheck,
        Actions;

    public string $title = 'Too long sentence check';

    public string $priority = 'medium';

    public int $timeToFix = 30;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = true;

    public ?string $failureReason;

    public mixed $actualValue = null;

    public mixed $expectedValue = null;

    public function check(Response $response, Crawler $crawler): bool
    {
        if ($this->validateContent($response, $crawler)) {
            return true;
        }

        return false;
    }

    public function validateContent(Response $response, Crawler $crawler): bool
    {
        $phrases = $this->extractPhrases(
            $this->getTextContent($response, $crawler)
        );

        $sentencesWithTooManyWords = $this->calculateSentencesWithTooManyWords($phrases);
        $this->actualValue = $sentencesWithTooManyWords;

        if (count($sentencesWithTooManyWords) === 0) {
            return true;
        }

        // If more than 20% of the total sentences are too long, fail
        if (count($sentencesWithTooManyWords) / count($phrases) > 0.2) {
            $this->failureReason = __('failed.content.too_long_sentence', [
                'actualValue' => count($this->actualValue),
            ]);

            return false;
        }

        return true;
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
