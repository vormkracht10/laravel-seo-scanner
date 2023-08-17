<?php

namespace Vormkracht10\Seo\Traits;

use Closure;
use Readability\Readability;
use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;

trait Actions
{
    private function getTextContent(Response $response, Crawler $crawler): string
    {
        $body = $response->body();

        if ($this->useJavascript) {
            $body = $crawler->filter('body')->html();
        }

        $readability = new Readability($body);

        $readability->init();

        return $readability->getContent()->textContent;
    }

    private function extractPhrases(string $content): array
    {
        // Get phrases seperate by new line, dot, exclamation mark or question mark
        return preg_split('/\n|\.|\!|\?/', $content);
    }
}
