<?php

namespace Vormkracht10\Seo\Checks\Content;

use Cscheide\ArticleExtractor\ArticleExtractor;
use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class ContentLengthCheck implements Check
{
    use PerformCheck;

    public string $title = 'Length of the content is at least 2100 characters';

    public string $priority = 'low';

    public int $timeToFix = 30;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = true;

    public function check(Response $response): bool
    {
        $content = $this->getContentToValidate($response);

        if (! $content) {
            return true;
        }

        return $this->validateContent($content);
    }

    public function getContentToValidate(Response $response): string|null
    {
        $extractor = new ArticleExtractor(null);

        $content = $response->body();

        return $extractor->processHTML($content)['text'] ?? null;
    }

    public function validateContent(string|array $content): bool
    {
        return strlen($content) >= 2100;
    }
}
