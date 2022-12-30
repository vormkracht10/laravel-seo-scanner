<?php

namespace Vormkracht10\Seo\Checks\Content;

use Illuminate\Http\Client\Response;
use Readability\Readability;
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

    public string $failureReason;

    public int|string $actualValue;

    public int|null $expectedValue = 2100;

    public function check(Response $response): bool
    {
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
        $this->actualValue = strlen($content);

        if (strlen($content) < $this->expectedValue) {
            $this->failureReason = __('failed.content.length', [
                'actualValue' => $this->actualValue,
                'expectedValue' => $this->expectedValue,
            ]);
        }

        return strlen($content) >= $this->expectedValue;
    }
}
