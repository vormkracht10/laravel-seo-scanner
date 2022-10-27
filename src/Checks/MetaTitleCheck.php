<?php

namespace Vormkracht10\Seo\Checks;

use Vormkracht10\Seo\Checks\Traits\ValidateResponse;

class MetaTitleCheck implements CheckInterface
{
    use ValidateResponse;

    public string $title = "Check if the title on the homepage does not contain 'home'";

    public string $priority = 'medium';

    public int $timeToFix = 1;
    
    public int $scoreWeight = 5;

    public bool $checkSuccessful = false;

    public function handle(string $url, object $response): self
    {
        $title = $this->getTitle($response);

        if (str_contains($title, 'home') || ! $title || ! $this->validateResponse($response)) {
            $this->checkSuccessful = false;

            return $this;
        }

        $this->checkSuccessful = true;

        return $this;
    }

    private function getTitle(object $response): string|null
    {
        $response = $response->body();
        preg_match('/<title>(.*)<\/title>/', $response, $matches);

        return $matches[1] ?? null;
    }
}
