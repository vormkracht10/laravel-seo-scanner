<?php

namespace Vormkracht10\Seo\Checks;

use Vormkracht10\Seo\Checks\Traits\ValidateResponse;

class MetaTitleLengthCheck implements CheckInterface
{
    use ValidateResponse;

    public string $title = 'Check if the title is not longer than 60 characters';

    public string $priority = 'medium';

    public int $timeToFix = 1;

    public bool $checkSuccessful = false;

    public function handle(string $url, object $response): self
    {
        $title = $this->getTitle($response);

        if (strlen($title) > 60 || ! $this->validateResponse($response)) {
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
