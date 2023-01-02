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

    public string|null $failureReason;

    public array|int|string|null $actualValue = null;

    public int|null|string $expectedValue = 1000000;

    public function check(Response $response): bool
    {
        $this->expectedValue = bytesToHumanReadable($this->expectedValue);

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

        $content = $crawler->filterXPath('//img')->each(function (Crawler $node, $i) {
            return $node->attr('src');
        });

        return collect($content)->filter(fn ($value) => $value !== null)->toArray();
    }

    public function validateContent(string|array $content): bool
    {
        if (! is_array($content)) {
            $content = [$content];
        }

        $links = [];

        $tooBigLinks = collect($content)->filter(function ($url) use (&$links) {
            if (! str_contains($url, 'http')) {
                $url = url($url);
            }

            $image = file_get_contents($url);

            if (strlen($image) > 1000000) {
                $size = bytesToHumanReadable(strlen($image));

                $links[] = $url.' (size: '.$size.')';

                return true;
            }

            return false;
        })->toArray();

        if (! empty($tooBigLinks)) {
            $this->actualValue = $links;

            $this->failureReason = __('failed.performance.image_size', [
                'actualValue' => implode(', ', $links),
                'expectedValue' => $this->expectedValue,
            ]);

            return false;
        }

        return true;
    }
}
