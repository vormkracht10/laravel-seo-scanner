<?php

namespace Vormkracht10\Seo\Checks\Performance;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class TTFBCheck implements Check
{
    use PerformCheck;

    public string $title = 'Time To First Byte (TTFB) is below 600 ms';

    public string $priority = 'high';

    public int $timeToFix = 15;

    public int $scoreWeight = 10;

    public bool $continueAfterFailure = true;

    public string|null $failureReason;

    public mixed $actualValue = null;

    public mixed $expectedValue = 0.6;

    public function check(Response $response, Crawler $crawler): bool
    {
        $ttfb = $response->transferStats?->getHandlerStats()['starttransfer_time'] ?? null;

        if (! $ttfb) {
            $this->failureReason = __('failed.performance.ttfb.missing');

            return false;
        }

        $this->actualValue = round($ttfb, 2);

        if ($this->actualValue <= 0.6) {
            return true;
        }

        $this->failureReason = __('failed.performance.ttfb', [
            'actualValue' => $this->actualValue,
            'expectedValue' => $this->expectedValue,
        ]);

        return false;
    }
}
