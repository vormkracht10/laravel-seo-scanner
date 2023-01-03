<?php

namespace Vormkracht10\Seo\Checks\Performance;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class HTMLSizeCheck implements Check
{
    use PerformCheck;

    public string $title = 'HTML is not larger than 100 KB';

    public string $priority = 'medium';

    public int $timeToFix = 60;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = true;

    public string|null $failureReason;

    public array|int|string|null $actualValue = null;

    public int|null|string $expectedValue = 100000;

    public function check(Response $response, Crawler $crawler): bool
    {
        $this->expectedValue = bytesToHumanReadable($this->expectedValue);

        if (! $this->validateContent($response)) {
            return false;
        }

        return true;
    }
     
    public function validateContent(Response $response): bool
    {
        $content = $response->body();

        if (! $content) {
            return false;
        }

        if (strlen($content) > 100000) {
            $this->actualValue = strlen($content);

            $this->failureReason = __('failed.performance.html_size', [
                'actualValue' => bytesToHumanReadable($this->actualValue),
                'expectedValue' => $this->expectedValue,
            ]);

            return false;
        }

        return strlen($content) < 100000;
    }
}
