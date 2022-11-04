<?php

namespace Vormkracht10\Seo\Checks;

use Vormkracht10\Seo\Checks\Traits\ValidateResponse;
use Closure;

class ResponseCheck
{
    use ValidateResponse;

    public string $title = 'Check if the response is successful';

    public string $priority = 'high';

    public int $timeToFix = 10;

    public int $scoreWeight = 5;

    public bool $checkSuccessful = false;

    public function handle($request, Closure $next)
    {
        if ($request->getStatusCode() === 200) {
            $this->checkSuccessful = true;

            return $next([$request, 'checks' => [$this]]);
        }
    }
}
