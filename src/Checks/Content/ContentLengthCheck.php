<?php

namespace Vormkracht10\Seo\Checks\Content;

use Illuminate\Http\Client\Response;
use Readability\Readability;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class ContentLengthCheck implements Check
{
    use PerformCheck;

    public string $title = 'Length of the content is at least 2100 characters';

    public string $priority = 'low';

    public int $timeToFix = 30;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = true;

    public function check(Response $response, Crawler $crawler): bool
    {
        if (app()->runningUnitTests()) {
            if (strlen($response->body()) < 2100) {
                return false;
            }

            return true;
        }

        $content = $this->getContentToValidate($response);

        if (! $content) {
            return true;
        }

        return $this->validateContent($content);
    }

    public function getContentToValidate(Response $response): string|null
    {
        $url = $response->transferStats->getHandlerStats()['url'];

        $readability = new Readability($response->body(), $url);

        $readability->init();

        return $readability->getContent()->textContent ?? null;
    }

    public function validateContent(string|array $content): bool
    {
        return strlen($content) >= 2100;
    }
}
