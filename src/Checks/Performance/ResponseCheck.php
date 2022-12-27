<?php

namespace Vormkracht10\Seo\Checks\Performance;

use Closure;
use Illuminate\Http\Client\Response;

class ResponseCheck
{
    public string $title = 'Check if the response is successful';

    public string $priority = 'high';

    public int $timeToFix = 10;

    public int $scoreWeight = 5;

    public bool $checkSuccessful = false;

    public function handle(Response $request, Closure $next): array
    {
        $this->checkSuccessful = false;

        if ($request->getStatusCode() === 200) {
            $this->checkSuccessful = true;
        }

        return $next([$request, 'checks' => [$this]]);
    }
}
