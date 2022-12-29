<?php

namespace Vormkracht10\Seo\Checks\Content;

use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class BrokenImageCheck implements Check
{
    use PerformCheck;

    public string $title = 'Check if links to images are broken';

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

        preg_match_all('/<img.*?src="(.*?)".*?>/msi', $response, $matches);

        return $matches[0] ?? null;
    }

    public function validateContent(string|array $content): bool
    {
        if (! is_array($content)) {
            $content = [$content];
        }

        $content = collect($content)->map(function ($link) {
            preg_match('/src="(.*?)"/msi', $link, $matches);

            return $matches[1] ?? false;
        })->filter(fn ($link) => checkIfLinkIsBroken($link));

        return count($content) === 0;
    }
}
