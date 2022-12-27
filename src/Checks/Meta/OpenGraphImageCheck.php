<?php

namespace Vormkracht10\Seo\Checks\Meta;

use Closure;
use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\MetaCheck;
use Vormkracht10\Seo\Traits\FormatRequest;

class OpenGraphImageCheck implements MetaCheck
{
    use FormatRequest;

    public string $title = 'Check if the page has an open graph image';

    public string $priority = 'medium';

    public int $timeToFix = 20;

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

    public function getContent(Response $response): string|null
    {
        $response = $response->body();

        preg_match('/<meta.*?property="og:image".*?content="(.*?)".*?>/msi', $response, $matches);

        return $matches[1] ?? null;
    }

    public function validateContent(string $content): bool
    {
        return ! $this->isBrokenLink($content);
    }

    public function isBrokenLink(string $url): bool
    {
        $ch = curl_init($url);

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_TIMEOUT => 10,
        ];

        curl_setopt_array($ch, $options);
        curl_exec($ch);

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return $statusCode !== 200;
    }
}
