<?php

namespace Vormkracht10\Seo\Checks;

use Closure;
use Vormkracht10\Seo\Checks\Traits\ValidateResponse;

class ResponseCheck implements CheckInterface
{
    public string $title = 'Check if the response is successful';

    public string $priority = 'high';

    public int $timeToFix = 10;

    public int $scoreWeight = 5;

    public bool $checkSuccessful = false;

    public function handle($request, Closure $next): Closure
    {
        if ($request->getStatusCode() === 200) {
            $this->checkSuccessful = true;

            return $next([$request, 'checks' => [$this]]);
        }
    }
}
