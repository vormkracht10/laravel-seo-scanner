<?php

namespace Vormkracht10\Seo\Checks\Content;

use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class MixedContentCheck implements Check
{
    use PerformCheck;

    public string $title = 'Check if links redirect to http while the page is on https';

    public string $priority = 'high';

    public int $timeToFix = 1;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = true;

    public function check(Response $response): bool
    {
        $content = $this->getContentToValidate($response);

        if (! $content || ! $this->validateContent($content)) {
            return false;
        }

        return true;
    }

    public function getContentToValidate(Response $response): string|array|null
    {
        $response = $response->body();

        preg_match_all('/<a.*?href="(.*?)".*?>/msi', $response, $matches);

        return $matches[1] ?? null;
    }

    public function validateContent(string|array $content): bool
    {
        if (! is_array($content)) {
            $content = [$content];
        }

        foreach ($content as $item) {
            if (preg_match('/^http:\/\//', $item)) {
                return false;
            }
        }

        return true;
    }
}
