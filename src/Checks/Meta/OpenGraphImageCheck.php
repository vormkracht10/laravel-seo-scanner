<?php

namespace Vormkracht10\Seo\Checks\Meta;

use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\Check;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Traits\PerformCheck;

class OpenGraphImageCheck implements Check
{
    use PerformCheck;

    public string $title = 'The page has an Open Graph image';

    public string $priority = 'medium';

    public int $timeToFix = 20;

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

        $crawler = new Crawler($response);

        $crawler = $crawler->filter('meta')->each(function (Crawler $node, $i) {
            $property = $node->attr('property');
            $content = $node->attr('content');

            if ($property === 'og:image') {
                return $content;
            }
        });

        return collect($crawler)->first(fn ($value) => $value !== null) ?? null;
    }

    public function validateContent(string $content): bool
    {
        return ! isBrokenLink($content);
    }
}
