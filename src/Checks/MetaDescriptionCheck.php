<?php

namespace Vormkracht10\Seo\Checks;

use Closure;
use Vormkracht10\Seo\Checks\Traits\FormatRequest;

class MetaDescriptionCheck implements CheckInterface
{
    use FormatRequest;

    public string $title = 'Check if the page has a meta description';

    public string $priority = 'medium';

    public int $timeToFix = 1;

    public int $scoreWeight = 5;

    public bool $checkSuccessful = false;

    public function handle($request, Closure $next): array
    {
        $description = $this->getDescription($request[0]);

        $this->checkSuccessful = false;

        if ($description) {
            $this->checkSuccessful = true;
        }

        return $next($this->formatRequest($request));
    }

    private function getDescription(object $response): string|null
    {
        $response = $response->body();
        preg_match('/<meta name="description" content="(.*)">/', $response, $matches);

        return $matches[1] ?? null;
    }
}
