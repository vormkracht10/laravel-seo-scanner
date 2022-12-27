<?php

namespace Vormkracht10\Seo\Checks\Content;

use Closure;
use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Traits\FormatRequest;
use Vormkracht10\Seo\Interfaces\ContentCheck;

class AltTagCheck implements ContentCheck
{
    use FormatRequest;

    public string $title = 'Check if every image has an alt tag';

    public string $priority = 'low';

    public int $timeToFix = 5;

    public int $scoreWeight = 5;

    public bool $checkSuccessful = false;

    public function handle(array $request, Closure $next): array
    {
        $content = $this->getContent($request[0]);

        if (! $content) {
            $this->checkSuccessful = true;

            return $next($this->formatRequest($request));
        }

        if (! $this->validateContent($content)) {
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
            if (! str_contains($image, 'alt=') || str_contains($image, 'alt=""')) {
                return false;
            }
        }
    
        return true;
    }
}
