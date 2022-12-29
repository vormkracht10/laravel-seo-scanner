<?php

namespace Vormkracht10\Seo\Checks\Meta;

use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class LangCheck implements Check
{
    use PerformCheck;

    public string $title = 'The lang attribute is set on the html tag';

    public string $priority = 'medium';

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

    public function getContentToValidate(Response $response): string|null
    {
        $response = $response->body();

        preg_match('/<html[^>]+>/i', $response, $matches);

        return $matches[0] ?? null;
    }

    public function validateContent(string $content): bool
    {
        return str_contains($content, 'lang=') && ! str_contains($content, 'lang=""');        
    }
}
