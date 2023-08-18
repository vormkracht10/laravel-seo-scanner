<?php

namespace Vormkracht10\Seo\Checks\Content;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\Actions;
use Vormkracht10\Seo\Traits\PerformCheck;

class FleschReadingEaseCheck implements Check
{
    use PerformCheck,
        Actions;

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
        if (! $this->validateContent($response, $crawler)) {
            return false;
        }

        return true;
    }

    public function validateContent(Response $response, Crawler $crawler): bool
    {
        $phrases = $this->extractPhrases(
            $this->getTextContent($response, $crawler)
        );

        // Remove all empty values
        $phrases = array_filter($phrases);

        // Average word count per sentence
        $averageWordCount = $this->getAverageWordCountPerSentence($phrases);

        // Average syllable count per word
        $averageSyllableCount = $this->getAverageSyllableCountPerWord($phrases);

        // Flesch Reading Ease score
        $fleschReadingEase = $this->fleschReadingEaseScoreFromAverages($averageSyllableCount, $averageWordCount);

        $this->actualValue = round($fleschReadingEase, 0, PHP_ROUND_HALF_UP);

        // 90-100	very easy to read, easily understood by an average 11-year-old student
        // 80-90	easy to read
        // 70-80	fairly easy to read
        // 60-70	easily understood by 13- to 15-year-old students
        // 50-60	fairly difficult to read
        // 30-50	difficult to read, best understood by college graduates
        // 0-30	very difficult to read, best understood by university graduates
        if ($fleschReadingEase < 60) {
            $this->failureReason = __('failed.content.flesch_reading_ease', [
                'actualValue' => $this->actualValue,
            ]);

            return false;
        }

        return true;
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

    private function getAverageSyllableCountPerWord(array $sentences): float
    {
        $totalSyllableCount = 0;
        $totalWordCount = 0;

        foreach ($sentences as $sentence) {
            $words = explode(' ', $sentence);

            foreach ($words as $word) {
                $totalSyllableCount += $this->countSyllables($word);
                $totalWordCount++;
            }
        }

        if ($totalWordCount > 0) {
            $averageSyllables = $totalSyllableCount / $totalWordCount;

            return round($averageSyllables, 2, PHP_ROUND_HALF_UP);
        } else {
            return 0; // Handle case of empty input
        }
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
