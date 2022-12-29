<?php

namespace Vormkracht10\Seo\Checks\Content;

use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class HTMLSizeCheck implements Check
{
    use PerformCheck;

    public string $title = 'Check if HTML is not bigger than 100 KB';

    public string $priority = 'medium';

    public int $timeToFix = 60;

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
        return $response->body();
    }

    public function validateContent(string|array $content): bool
    {
        return strlen($content) < 100000;
    }
}
