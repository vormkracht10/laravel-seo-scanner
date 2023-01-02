<?php

namespace Vormkracht10\Seo\Checks\Performance;

use Illuminate\Http\Client\Response;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class JavascriptSizeCheck implements Check
{
    use PerformCheck;

    public string $title = 'Javascript files are not bigger than 1 MB';

    public string $priority = 'medium';

    public int $timeToFix = 60;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = true;

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

        $crawler = $crawler->filter('script')->each(function (Crawler $node, $i) {
            $src = $node->attr('src');

            if ($src) {
                return $src;
            }
        });

        return collect($crawler)->filter(fn ($value) => $value !== null)->toArray();
    }

    public function validateContent(string|array $content): bool
    {
        if (! is_array($content)) {
            $content = [$content];
        }

        foreach ($content as $url) {
            if (! $url) {
                continue;
            }

            if (! str_contains($url, 'http')) {
                $url = url($url);
            }

            if (isBrokenLink(url: $url)) {
                continue;
            }

            $size = getRemoteFileSize(url: $url);

            /**
             * @todo this one fails when we have no access to the content length
             * header. This happens when we try to access an external resource
             * like Google Tag Manager. We should decide on how to get
             * the size of the file in this case. Or if we should
             * even check the size of external resources.
             */
            if (! $size || $size > 1000000) {
                return false;
            }
        }

        return true;
    }
}
