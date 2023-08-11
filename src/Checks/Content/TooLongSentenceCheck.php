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

        foreach ($sentences as $sentence) {

            // If the sentence contains a dot, followed by a space, followed by a capital letter, it's a real sentence. 
            // This is not 100% correct, but it's good enough for now.
            if (! preg_match('/\.\s[A-Z]/', $sentence)) {
                continue;
            }

            $sentence = explode('.', $sentence);
            $realSentences = array_merge($realSentences, $sentence);
        }

        $sentences = $realSentences;

        $this->actualValue = $this->calculateSentencesWithTooManyWords($sentences);

        if ($this->actualValue > 1) {
            $this->failureReason = __('failed.content.too_long_sentence', [
                'actualValue' => count($this->actualValue),
            ]);

            return false;
        }

        return true;
    }

    public function getSentencesFromCrawler(Crawler $crawler): array 
    {
        $content = $crawler->filterXPath('//body')->children();

        // Get all elements that contain text
        $content = $content->filterXPath('//*/text()[normalize-space()]');

        $content = $content->each(function (Crawler $node, $i) {
            return $node->text();
        });

        return $content;
    }

    public function calculateSentencesWithTooManyWords(array $sentences): array
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