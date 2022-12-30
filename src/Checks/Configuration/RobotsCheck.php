<?php

namespace Vormkracht10\Seo\Checks\Configuration;

use Illuminate\Http\Client\Response;
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

    public function check(Response $response): bool
    {
        $url = $response->transferStats->getHandlerStats()['url'];

        $client = new UriClient($url);

        return $client->userAgent('Googlebot')->isAllowed($url);
    }
}
