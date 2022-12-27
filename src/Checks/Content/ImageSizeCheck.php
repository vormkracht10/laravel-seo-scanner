<?php

namespace Vormkracht10\Seo\Checks\Content;

use Closure;
use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\ContentCheck;
use Vormkracht10\Seo\Traits\FormatRequest;

class ImageSizeCheck implements ContentCheck
{
    use FormatRequest;

    public string $title = 'Check if images are not bigger than 1 MB';

    public string $priority = 'high';

    public int $timeToFix = 10;

    public int $scoreWeight = 5;

    public bool $checkSuccessful = false;

    public function handle(array $request, Closure $next): array
    {
        $content = $this->getContent($request[0]);

        if (! $content || ! $this->validateContent($content)) {
            return $next($this->formatRequest($request));
        }

        $this->checkSuccessful = true;

        return $next($this->formatRequest($request));
    }

    public function getContent(Response $response): string|array|null
    {
        $response = $response->body();

        preg_match_all('/<img[^>]+>/i', $response, $matches);

        return $matches[0] ?? null;
    }

    public function validateContent(string|array $content): bool
    {
        if (! is_array($content)) {
            $content = [$content];
        }

        foreach ($content as $image) {
            preg_match('/src="([^"]+)"/', $image, $match);

            if (! $match) {
                continue;
            }

            $image = $match[1];

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
