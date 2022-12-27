<?php

namespace Vormkracht10\Seo\Checks\Content;

use Closure;
use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\ContentCheck;
use Vormkracht10\Seo\Traits\FormatRequest;

class MultipleHeadingCheck implements ContentCheck
{
    use FormatRequest;

    public string $title = 'Check if none or multiple H1 headings are used';

    public string $priority = 'low';

    public int $timeToFix = 1;

    public int $scoreWeight = 5;

    public bool $checkSuccessful = false;

    public function handle(array $request, Closure $next): array
    {
        $content = $this->getContent($request[0]);

        // If no H1 headings are found, the check also fails because it is an important SEO element.
        if (! $content || ! $this->validateContent($content)) {
            return $next($this->formatRequest($request));
        }

        $this->checkSuccessful = true;

        return $next($this->formatRequest($request));
    }

    public function getContent(Response $response): string|array|null
    {
        $response = $response->body();

        preg_match_all('/<h1.*?>(.*)<\/h1>/msi', $response, $matches);

        return $matches[1] ?? null;
    }

    public function validateContent(string|array $content): bool
    {
        if (is_array($content) && count($content) > 1) {
            return false;
        }

        return true;
    }
}
