<?php

namespace Vormkracht10\Seo\Checks\Performance;

use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\Check;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Traits\PerformCheck;

class ImageSizeCheck implements Check
{
    use PerformCheck;

    public string $title = 'Images are not larger than 1 MB';

    public string $priority = 'high';

    public int $timeToFix = 10;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = true;

    public function check(Response $response): bool
    {
        $content = $this->getContentToValidate($response);

        if (! $content) {
            return true;
        }

        if ($this->validateContent($content)) {
            return false;
        }

        return true;
    }

    public function getContentToValidate(Response $response): string|array|null
    {
        $response = $response->body();

        $crawler = new Crawler($response);

        $matches = $crawler->filter('img')->each(function (Crawler $node, $i) {
            return $node->attr('src');
        });

        return collect($matches)->filter(fn ($value) => $value !== null)->toArray();
    }

    public function validateContent(string|array $content): bool
    {
        if (! is_array($content)) {
            $content = [$content];
        }

        foreach ($content as $image) {
            if (! str_contains($image, 'http')) {
                $image = url($image);
            }

            $image = file_get_contents($image);

            if (strlen($image) > 1000000) {
                return false;
            }
        }

        return true;
    }
}
