<?php

namespace Vormkracht10\Seo\Checks;

use Closure;
use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Checks\Traits\FormatRequest;

class MetaTitleLengthCheck implements CheckInterface
{
    use FormatRequest;

    public string $title = 'Check if the title is not longer than 60 characters';

    public string $priority = 'medium';

    public int $timeToFix = 1;

    public int $scoreWeight = 5;

    public bool $checkSuccessful = false;

    public function handle(array $request, Closure $next): array
    {
        $title = $this->getTitle($request[0]);

        if (! $title) {
            return $next($this->formatRequest($request));
        }

        if (strlen($title) <= 60) {
            $this->checkSuccessful = true;
        }

        return $next($this->formatRequest($request));
    }

    private function getTitle(Response $response): string|null
    {
        $response = $response->body();
        preg_match('/<title>(.*)<\/title>/', $response, $matches);

        return $matches[1] ?? null;
    }
}
