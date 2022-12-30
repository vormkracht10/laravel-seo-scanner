<?php

namespace Vormkracht10\Seo\Checks\Performance;

use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class JavascriptSizeCheck implements Check
{
    use PerformCheck;

    public string $title = 'Javascript files are not bigger than 1 MB';

    public string $priority = 'medium';

    public int $timeToFix = 60;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = true;

    public function check(Response $response): bool
    {
        $content = $this->getContentToValidate($response);

        if (! $content) {
            return true;
        }

        if (! $this->validateContent($content)) {
            return false;
        }

        return true;
    }

    public function getContentToValidate(Response $response): string|array|null
    {
        $response = $response->body();

        preg_match_all('/<script[^>]+>/i', $response, $matches);

        $links = collect($matches[0])
            ->filter(function ($link) {
                return str_contains($link, 'src=');
            })
            ->map(function ($link) {
                // Get the src attribute
                preg_match('/src="([^"]+)"/', $link, $matches);

                if (isset($matches[1]) && ! $matches[1]) {
                    // Get part after src= and before the first whitespace or >
                    // This is needed for inline scripts that don't have quotes around the src
                    preg_match('/src=([^ >]+)/', $link, $matches);
                }

                return $matches[1] ?? null;
            })
            ->filter()
            ->toArray();

        return $links;
    }

    public function validateContent(string|array $content): bool
    {
        if (! is_array($content)) {
            $content = [$content];
        }

        foreach ($content as $url) {
            if (! $url) {
                continue;
            }

            if (! str_contains($url, 'http')) {
                $url = url($url);
            }

            if (isBrokenLink(url: $url)) {
                continue;
            }

            $size = getRemoteFileSize(url: $url);

            if (! $size || $size > 1000000) {
                return false;
            }
        }

        return true;
    }
}
