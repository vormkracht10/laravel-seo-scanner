<?php

namespace Vormkracht10\Seo\Checks\Meta;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
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

    public ?string $failureReason;

    public mixed $actualValue = null;

    public mixed $expectedValue = null;

    public function check(Response $response, Crawler $crawler): bool
    {
        if (! $this->validateContent($crawler)) {
            return false;
        }

        return true;
    }

    public function validateContent(Crawler $crawler): bool
    {
        $node = $crawler->filterXPath('//title')->getNode(0);

        if (! $node) {
            return false;
        }

        $content = $crawler->filterXPath('//title')->text();

        if (! $content) {
            $this->failureReason = __('failed.meta.title.no_content');

            return false;
        }

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
