<?php

namespace Vormkracht10\Seo\Checks\Content;

use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class MultipleHeadingCheck implements Check
{
    use PerformCheck;

    public string $title = 'Check if none or multiple H1 headings are used';

    public string $priority = 'low';

    public int $timeToFix = 1;

    public int $scoreWeight = 5;

    public function check(Response $response): bool
    {
        $content = $this->getContentToValidate($response);

        // If no H1 headings are found, the check also fails because it is an important SEO element.
        if (! $content || ! $this->validateContent($content)) {
            return false;
        }

        return true;
    }

    public function getContentToValidate(Response $response): string|array|null
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
