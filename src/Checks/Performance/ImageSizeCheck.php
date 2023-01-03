<?php

namespace Vormkracht10\Seo\Checks\Performance;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class ImageSizeCheck implements Check
{
    use PerformCheck;

    public string $title = 'Images are not larger than 1 MB';

    public string $priority = 'high';

    public int $timeToFix = 10;

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
        $crawler = $crawler->filterXPath('//img')->each(function (Crawler $node, $i) {
            return $node->attr('src');
        });

        $content = collect($crawler)->filter(fn ($value) => $value !== null)->toArray();

        if (! $content) {
            return true;
        }

        foreach ($content as $image) {
            if (! str_contains($image, 'http')) {
                $image = url($image);
            }

            if (isBrokenLink($image)) {
                return false;
            }

            $image = file_get_contents($image);

            if (strlen($image) > 1000000) {
                return false;
            }
        }

        return true;
    }
}
