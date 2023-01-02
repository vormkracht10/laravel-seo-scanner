<?php

namespace Vormkracht10\Seo\Checks\Content;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class MixedContentCheck implements Check
{
    use PerformCheck;

    public string $title = 'All links redirect to an url using HTTPS';

    public string $priority = 'high';

    public int $timeToFix = 1;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = true;

    public string|null $failureReason;

    public int|string|null $actualValue = null;

    public int|null $expectedValue = null;

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

        $crawler = new Crawler($response);

        $content = $crawler->filterXPath('//a')->each(function (Crawler $node, $i) {
            return $node->attr('href');
        });

        return $content;
    }

    public function validateContent(string|array $content): bool
    {
        if (! is_array($content)) {
            $content = [$content];
        }

        $links = [];

        $nonSecureLinks = collect($content)->filter(function ($item) use (&$links) {
            if (preg_match('/^http:\/\//', $item)) {
                $links[] = $item;

                return true;
            }
            
            return false;
        });

        if ($nonSecureLinks->count() > 0) {
            $this->failureReason = __('failed.content.mixed_content', [
                'links' => implode(', ', $links),
            ]);

            return false;
        }

        return true;
    }
}
