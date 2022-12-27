<?php

namespace Vormkracht10\Seo\Checks;

use Closure;
use Illuminate\Http\Client\Response;

class ResponseCheck
{
    /** @var string */
    public string $title = 'Check if the response is successful';

    /** @var string */
    public string $priority = 'high';

    /** @var int */
    public int $timeToFix = 10;

    /** @var int */
    public int $scoreWeight = 5;

    /** @var bool */
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
