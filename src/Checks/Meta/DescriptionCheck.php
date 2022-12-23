<?php

namespace Vormkracht10\Seo\Checks\Meta;

use Closure;
use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Checks\Traits\FormatRequest;

class DescriptionCheck implements MetaCheck
{
    use FormatRequest;

    public string $title = 'Check if the page has a meta description';

    public string $priority = 'medium';

    public int $timeToFix = 1;

    public int $scoreWeight = 5;

    public bool $checkSuccessful = false;

    public function handle(array $request, Closure $next): array
    {
        $content = $this->getContent($request[0]);

        if (! $content || ! $this->validateContent($content)) {
            return $next($this->formatRequest($request));
        }

        $this->checkSuccessful = true;

        return $next($this->formatRequest($request));
    }

    public function getContent(Response $response): string|null
    {
        $response = $response->body();
        preg_match('/<meta name="description" content="(.*)">/', $response, $matches);

        return $matches[1] ?? null;
    }

    public function validateContent(string $content): bool
    {
        return strlen($content) > 0;
    }
}
