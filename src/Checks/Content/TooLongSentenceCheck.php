<?php

namespace Vormkracht10\Seo\Checks\Content;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Helpers\TransitionWords;
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
        $sentences = $this->getSentencesFromCrawler($crawler);

        // Loop through all html elements in $sentences

        // dd($sentences);

        // Remove sentences that cannot be seen as real sentences
        $content = $crawler->filterXPath('//body')->children()->each(function (Crawler $node, $i) {
            $node->children()->each(function (Crawler $node, $i) {
                dump($node->text());
            });
        });

        // Loop through all HTML elements
        

        dd('');

        // TODO: Get the content which is all the text in the body tag but inside a tag. Also get them by tag
        $ccontent = $crawler->filterXPath('//body')->children()->each(function (Crawler $node, $i) {
            $node->children()->each(function (Crawler $node, $i) {
                dump($node->html());
            });
        });

        dd($content);

        $this->actualValue = $this->calculateSentencesWithTooManyWords($content);

        dd($this->actualValue);

        if ($this->actualValue > 1) {
            $this->failureReason = __('failed.content.too_long_sentence', [
                'actualValue' => $this->actualValue,
            ]);

            return false;
        }

        return true;
    }

    public function getSentencesFromCrawler(Crawler $crawler): array {
        $sentences = [];

        // Select all elements that contain text
        $elements = $crawler->filterXPath('//*/text()[normalize-space()]');

        // Loop through each element and split the text into sentences
        foreach ($elements as $element) {
            $text = trim($element->textContent);

            // Exclude any text that is inside HTML tags
            $text = preg_replace('/<[^>]*>/', '', $text);

            // Exclude any text that is inside JavaScript
            $text = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $text);

            // Exclude any text that contains '(function(' or 'window.' 

            // Split the remaining text into sentences
            $matches = preg_split('/(?<=[.?!])\s+(?=[a-z])/i', $text);
            $sentences = array_merge($sentences, $matches);
        }

        return $sentences;
    }

    public function calculateSentencesWithTooManyWords($content)
    {
        $totalSentences = preg_match_all('/\b[\w\s]+\b/', $content, $matches);

        if ($totalSentences === 0) {
            $this->actualValue = 0;

            return 0;
        }

        $sentencesWithTooManyWords = 0;

        foreach ($matches[0] as $match) {
            if (str_word_count($match) > 20 ? 1 : 0) {
                dump($match);
            }
            $sentencesWithTooManyWords += str_word_count($match) > 20 ? 1 : 0;
        }

        return $sentencesWithTooManyWords;
    }
}