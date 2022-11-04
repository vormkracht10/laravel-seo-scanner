<?php

namespace Vormkracht10\Seo\Checks;

use Closure;
use Vormkracht10\Seo\Checks\Traits\FormatRequest;

class MetaTitleCheck implements CheckInterface
{
    use FormatRequest;

    public string $title = "Check if the title on the homepage does not contain 'home'";

    public string $priority = 'medium';

    public int $timeToFix = 1;

    public int $scoreWeight = 5;

    public bool $checkSuccessful = false;

    public function handle($request, Closure $next): array
    {
        $title = $this->getTitle($request[0]);

        $this->checkSuccessful = false;

        if (! str_contains($title, 'home') || $title) {
            $this->checkSuccessful = true;
        }

        return $next($this->formatRequest($request));
    }

    private function getTitle(object $response): string|null
    {
        $response = $response->body();
        preg_match('/<title>(.*)<\/title>/', $response, $matches);

        return $matches[1] ?? null;
    }
}
