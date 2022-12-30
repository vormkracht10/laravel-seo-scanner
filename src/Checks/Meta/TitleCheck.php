<?php

namespace Vormkracht10\Seo\Checks\Meta;

use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class TitleCheck implements Check
{
    use PerformCheck;

    public string $title = "The page title does not contain 'home' or 'homepage'";

    public string $priority = 'medium';

    public int $timeToFix = 1;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = true;

    public string $failureReason;

    public int|string|null $actualValue = null;

    public int|null $expectedValue = null;

    public function check(Response $response): bool
    {
        $content = $this->getContentToValidate($response);

        if (! $content) {
            $this->failureReason = __('failed.meta.title.no_content');

            return false;
        }

        if (! $this->validateContent($content)) {
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
        $content = strtolower($content);

        if (str_contains($content, 'home') || str_contains($content, 'homepage')) {
            $this->actualValue = $content;

            $this->failureReason = __('failed.meta.title', [
                'actualValue' => $this->actualValue,
            ]);

            return false;
        }

        return true;
    }
}
