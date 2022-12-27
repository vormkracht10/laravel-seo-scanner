<?php

namespace Vormkracht10\Seo\Checks\Content;

use Closure;
use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\ContentCheck;
use Vormkracht10\Seo\Traits\FormatRequest;

class HTMLSizeCheck implements ContentCheck
{
    use FormatRequest;

    public string $title = 'Check if HTML is not bigger than 100 KB';

    public string $priority = 'medium';

    public int $timeToFix = 60;

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

    public function getContent(Response $response): string|array|null
    {
        return $response->body();
    }

    public function validateContent(string|array $content): bool
    {
        return strlen($content) < 100000;
    }
}
