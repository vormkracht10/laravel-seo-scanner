<?php

namespace Vormkracht10\Seo\Checks\Meta;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class DescriptionCheck implements Check
{
    use PerformCheck;

    public string $title = 'The page has a meta description';

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
            $this->failureReason = __('failed.meta.description');

            return false;
        }

        return true;
    }

    public function getDescriptionContent(Crawler $crawler): ?string
    {
        $tags = ['description', 'og:description', 'twitter:description'];
    
        foreach ($tags as $tag) {
            $property = $tag === 'og:description' ? 'property' : 'name';
            
            /** @var \DOMElement $node */
            $node = $crawler->filterXPath("//meta[@{$property}=\"{$tag}\"]")->getNode(0);
    
            if ($node) {
                return $node->getAttribute('content');
            }
        }
    
        return null;
    }
    
    public function validateContent(Crawler $crawler): bool
    {
        $content = $this->getDescriptionContent($crawler);
    
        return !empty($content);
    }
    
    public function isDescriptionSet(Crawler $crawler): bool
    {
        return $this->getDescriptionContent($crawler) !== null;
    }
}
