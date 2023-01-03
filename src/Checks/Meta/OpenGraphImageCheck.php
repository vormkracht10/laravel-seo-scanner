<?php

namespace Vormkracht10\Seo\Checks\Meta;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class OpenGraphImageCheck implements Check
{
    use PerformCheck;

    public string $title = 'The page has an Open Graph image';

    public string $priority = 'medium';

    public int $timeToFix = 20;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = true;

    public function check(Response $response, Crawler $crawler): bool
    {
        if (! $this->validateContent($crawler)) {
            return false;
        }

        return true;
    }

    public function validateContent(Crawler $crawler): bool
    {
        $crawler = $crawler->filterXPath('//meta')->each(function (Crawler $node, $i) {
            $property = $node->attr('property');
            $content = $node->attr('content');

            if ($property === 'og:image') {
                return $content;
            }
        });

        $content = (string) collect($crawler)->first(fn ($value) => $value !== null);

        if (! $content) {
            return false;
        }

        return ! isBrokenLink($content);
    }
}
