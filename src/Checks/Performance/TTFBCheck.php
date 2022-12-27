<?php

namespace Vormkracht10\Seo\Checks\Performance;

use Closure;
use Vormkracht10\Seo\Traits\FormatRequest;

class TTFBCheck
{
    use FormatRequest;

    public string $title = "Check if 'Time To First Byte' is below 600 ms";

    public string $priority = 'high';

    public int $timeToFix = 15;

    public int $scoreWeight = 10;

    public bool $checkSuccessful = false;

    public function handle(array $request, Closure $next): array
    {
        $ttfb = $request[0]->transferStats->getHandlerStats()['starttransfer_time'] ?? 0;

        if ($ttfb < 0.6) {
            $this->checkSuccessful = true;
        }

        return $next($this->formatRequest($request));
    }
}
