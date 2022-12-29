<?php

namespace Vormkracht10\Seo\Checks\Configuration;

use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class NoFollowCheck implements Check
{
    use PerformCheck;

    public string $title = 'NoFollow is not set on the page';

    public string $priority = 'low';

    public int $timeToFix = 5;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = false;

    public function check(Response $response): bool
    {
        $content = $this->getContentToValidate($response);

        if ($response->header('X-Robots-Tag') === 'nofollow') {
            return false;
        }

        if (! $content) {
            return true;
        }

        if (! $this->validateContent($content)) {
            return false;
        }

        return true;
    }

    public function getContentToValidate(Response $response): array|null
    {
        $response = $response->body();

        preg_match_all('/<meta[^>]+>/i', $response, $matches);

        $metaTags = array_filter($matches[0], function ($metaTag) {
            return str_contains($metaTag, 'name="robots"') ||
                str_contains($metaTag, 'name="googlebot"') ||
                str_contains($metaTag, "name='robots'") ||
                str_contains($metaTag, "name='googlebot'");
        });

        $metaTags = array_map(function ($metaTag) {
            preg_match('/content="([^"]+)"/', $metaTag, $matches);

            $matches = $matches[1] ?? null;

            if ($matches) {
                return strtolower($matches);
            }

            return null;
            
        }, $metaTags);

        return $metaTags;
    }

    public function validateContent(array $content): bool
    {
        foreach ($content as $metaTag) {
            if (str_contains($metaTag, 'nofollow')) {
                return false;
            }
        }

        return true;
    }
}
