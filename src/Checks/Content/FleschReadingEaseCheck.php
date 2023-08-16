<?php

namespace Vormkracht10\Seo\Checks\Content;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class FleschReadingEaseCheck implements Check
{
    use PerformCheck;

    public string $title = 'Flesch Reading Ease';

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
        $sentences = $this->getSentencesFromCrawler($crawler);

        dd($sentences);
        $sentences = $this->separateSentencesByDot($sentences);

        // Average word count per sentence
        $averageWordCount = $this->getAverageWordCountPerSentence($sentences);

        // Average syllable count per word
        $averageSyllableCount = $this->getAverageSyllableCountPerWord($sentences);

        $fleschReadingEase = $this->fleschReadingEaseScoreFromAverages($averageSyllableCount, $averageWordCount);

        // TODO:
        // Average word count is too low and the average syllable count is too high. That's why the calculation is not accurate.
        // Probably because we still get sentences like: cls-11{stroke:#fe8185}
        // We need to find a better way to get all sentences from a web page.
        dd($averageSyllableCount, $averageWordCount, $fleschReadingEase);

        // return true;
    }

    private function fleschReadingEaseScoreFromAverages(float $averageSyllableCount, float $averageWordCount): float
    {
        $fleschReadingEase = 206.835 - (1.015 * $averageWordCount) - (84.6 * $averageSyllableCount);

        return $fleschReadingEase;
    }

    private function getAverageWordCountPerSentence(array $sentences): int
    {
        $totalWordCount = 0;

        foreach ($sentences as $sentence) {
            $totalWordCount += str_word_count($sentence);
        }

        return round($totalWordCount / count($sentences), 0, PHP_ROUND_HALF_UP);
    }

    private function getAverageSyllableCountPerWord(array $sentences): int
    {
        $totalSyllableCount = 0;

        foreach ($sentences as $sentence) {
            $words = explode(' ', $sentence);

            foreach ($words as $word) {
                $totalSyllableCount += $this->averageSyllablesPerWord($word);
            }
        }

        return round($totalSyllableCount / count($sentences), 0, PHP_ROUND_HALF_UP);
    }

    private function countSyllables($word)
    {
        $vowels = ['a', 'e', 'i', 'o', 'u', 'y', 'A', 'E', 'I', 'O', 'U', 'Y'];
        $syllables = 0;
        $prevChar = null;

        for ($i = 0; $i < strlen($word); $i++) {
            $char = $word[$i];
            if (in_array($char, $vowels) && ($prevChar === null || ! in_array($prevChar, $vowels))) {
                $syllables++;
            }
            $prevChar = $char;
        }

        return max(1, $syllables); // Ensure at least one syllable for non-empty word
    }

    private function averageSyllablesPerWord($text)
    {
        $words = preg_split('/\s+/', $text);
        $totalSyllables = array_reduce($words, function ($carry, $word) {
            return $carry + $this->countSyllables($word);
        }, 0);
        $totalWords = count($words);

        if ($totalWords > 0) {
            $averageSyllables = $totalSyllables / $totalWords;

            return $averageSyllables;
        } else {
            return 0; // Handle case of empty input
        }
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
            return strip_tags($node->text());
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
