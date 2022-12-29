<?php

namespace Vormkracht10\Seo\Checks\Content;

use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class BrokenLinkCheck implements Check
{
    use PerformCheck;

    public string $title = 'The page contains no broken links';

    public string $priority = 'medium';

    public int $timeToFix = 10;

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

        preg_match_all('/<a.*?href="(.*?)".*?>/msi', $response, $matches);

        return $matches[0] ?? null;
    }

    public function validateContent(string|array $content): bool
    {
        if (! is_array($content)) {
            $content = [$content];
        }

        $content = collect($content)->map(function ($item) {
            preg_match('/href="(.*?)"/msi', $item, $matches);

            return $matches[1] ?? false;
        })->filter(function ($link) {
            // Filter out all links that are mailto, tel or have a file extension
            if (preg_match('/^mailto:/msi', $link) ||
                preg_match('/^tel:/msi', $link) ||
                preg_match('/\.[a-z]{2,4}$/msi', $link) ||
                filter_var($link, FILTER_VALIDATE_URL) === false
            ) {
                return false;
            }

            return $link;
        })->filter(fn ($link) => isBrokenLink($link))->toArray();

        return count($content) === 0;
    }
}
