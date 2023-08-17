<?php

namespace Vormkracht10\Seo\Checks\Content;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class AltTagCheck implements Check
{
    use PerformCheck;

    public string $title = 'Every image has an alt tag';

    public string $priority = 'low';

    public int $timeToFix = 5;

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
        $imagesWithoutAlt = $crawler->filterXPath('//img[not(@alt)]')->each(function (Crawler $node, $i) {
            return $this->filterImage($node);
        });
        $imagesWithEmptyAlt = $crawler->filterXPath('//img[@alt=""]')->each(function (Crawler $node, $i) {
            return $this->filterImage($node);
        });

        // Remove null values from the arrays
        $imagesWithoutAlt = array_filter($imagesWithoutAlt);
        $imagesWithEmptyAlt = array_filter($imagesWithEmptyAlt);

        $imagesWithoutAlt = array_merge($imagesWithoutAlt, $imagesWithEmptyAlt);

        $this->actualValue = $imagesWithoutAlt;

        if (count($imagesWithoutAlt) > 0) {
            $this->failureReason = __('failed.content.alt_tag', [
                'actualValue' => implode(', ', $imagesWithoutAlt),
            ]);

            return false;
        }

        return true;
    }

    private function filterImage($node): ?string
    {
        $src = $node->attr('src');

        if (str_contains($src, '.svg')) {
            return $src;
        }

        $dimensions = $this->getImageDimensions($src, $node);

        if ($dimensions['width'] < 5 || $dimensions['height'] < 5) {
            return null;
        }

        return $src;
    }

    private function getImageDimensions(string $src, Crawler $node): array
    {
        if (app()->runningUnitTests()) {
            return [
                'width' => $node->attr('width'),
                'height' => $node->attr('height'),
            ];
        }

        $dimensions = getimagesize($src);

        return [
            'width' => $dimensions[0],
            'height' => $dimensions[1],
        ];
    }
}
