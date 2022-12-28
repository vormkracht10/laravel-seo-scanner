<?php

namespace Vormkracht10\Seo\Checks\Meta;

use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class TitleLengthCheck implements Check
{
    use PerformCheck;

    public string $title = 'Check if the title is not longer than 60 characters';

    public string $priority = 'medium';

    public int $timeToFix = 1;

    public int $scoreWeight = 5;

    public function check(Response $response): bool
    {
        $content = $this->getContentToValidate($response);

        if (! $content || ! $this->validateContent($content)) {
            return false;
        }

        return true;
    }

    public function getContentToValidate(Response $response): string|null
    {
        $response = $response->body();
        preg_match('/<title>(.*)<\/title>/', $response, $matches);

        return $matches[1] ?? null;
    }

    public function validateContent(string $content): bool
    {
        return strlen($content) <= 60;
    }
}
