<?php

namespace Vormkracht10\Seo\Checks\Performance;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class CompressionCheck implements Check
{
    use PerformCheck;

    public string $title = 'HTML is GZIP compressed';

    public string $priority = 'high';

    public int $timeToFix = 15;

    public int $scoreWeight = 10;

    public bool $continueAfterFailure = true;

    public string|null $failureReason;

    public mixed $actualValue = null;

    public mixed $expectedValue = null;

    public function check(Response $response, Crawler $crawler): bool
    {
        if (! in_array($response->header('Content-Encoding'), ['gzip', 'compress', 'deflate', 'br'])) {
            $this->failureReason = __('failed.performance.compression');

            return false;
        }
    }
}
