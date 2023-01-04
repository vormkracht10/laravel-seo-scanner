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

    public string|null $failureReason;

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
        $crawler = $crawler->filterXPath('//meta')->each(function (Crawler $node, $i) {
            $property = $node->attr('property');
            $content = $node->attr('content');

            if ($property === 'og:image') {
                return $content;
            }
        });

        $content = (string) collect($crawler)->first(fn ($value) => $value !== null);

        $this->actualValue = $content;

        if (! $content) {
            $this->failureReason = __('failed.meta.open_graph_image');
            
            return false;
        }

        if (isBrokenLink($content)) {
            $this->failureReason = __('failed.meta.open_graph_image.broken', [
                'actualValue' => $content,
            ]);

            return false;
        }
        
        return true;
    }
}
