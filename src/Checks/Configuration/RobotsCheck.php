<?php

namespace Vormkracht10\Seo\Checks\Configuration;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use vipnytt\RobotsTxtParser\UriClient;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class RobotsCheck implements Check
{
    use PerformCheck;

    public string $title = 'Robots.txt allows indexing';

    public string $priority = 'low';

    public int $timeToFix = 5;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = false;

    public ?string $failureReason;

    public mixed $actualValue = null;

    public mixed $expectedValue = null;

    public function check(Response $response, Crawler $crawler): bool
    {
        $url = $response->transferStats?->getHandlerStats()['url'] ?? null;

        if (! $url) {
            $this->failureReason = __('failed.configuration.robots.missing_url');

            return false;
        }

        $client = new UriClient($url);

        if (! $client->userAgent('Googlebot')->isAllowed($url)) {
            $this->failureReason = __('failed.configuration.robots.disallowed');

            return false;
        }

        return true;
    }
}
