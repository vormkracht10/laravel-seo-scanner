<?php

namespace Vormkracht10\Seo\Checks\Performance;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;
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

    public mixed $expectedValue = ['gzip', 'compress', 'deflate', 'br'];

    public function check(Response $response, Crawler $crawler): bool
    {
        $encodingHeader = collect($response->headers())->filter(function ($value, $key) {
            return Str::contains($key, 'Content-Encoding') || Str::contains($key, 'x-encoded-content-encoding');
        })->filter(function ($values) {
            $header = collect($values)->filter(function ($value) {
                return in_array($value, $this->expectedValue);
            });

            return ! $header->isEmpty();
        });

        if ($encodingHeader->isEmpty()) {
            $this->failureReason = __('failed.performance.compression');

            return false;
        }

        return true;
    }
}
