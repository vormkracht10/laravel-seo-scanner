<?php

namespace Vormkracht10\Seo\Checks\Performance;

use Closure;
use Illuminate\Http\Client\Response;

class ResponseCheck
{
    /** @var string $title */
    public string $title = 'Check if the response is successful';

    /** @var string $priority */
    public string $priority = 'high';

    /** @var int $timeToFix */
    public int $timeToFix = 10;

    /** @var int $scoreWeight */
    public int $scoreWeight = 5;

    /** @var bool $checkSuccessful */
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
