<?php

namespace Vormkracht10\Seo\Checks\Content;

use Closure;
use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Checks\Traits\FormatRequest;

class MixedContentCheck implements ContentCheck
{
    use FormatRequest;

    public string $title = 'Check if links redirect to http while the page is on https';

    public string $priority = 'high';

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

    public function getContent(Response $response): string|array|null
    {
        $response = $response->body();

        preg_match_all('/<a.*?href="(.*?)".*?>/msi', $response, $matches);

        return $matches[1] ?? null;
    }

    public function validateContent(string|array $content): bool
    {
        if (is_array($content)) {
            foreach ($content as $item) {
                if (preg_match('/^http:\/\//', $item)) {
                    return false;
                }
            }

            return true;
        }

        if (preg_match('/^http:\/\//', $content)) {
            return false;
        }

        return true;
    }
}
