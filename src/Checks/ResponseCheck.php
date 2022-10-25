<?php

namespace Vormkracht10\Seo\Checks;

use Vormkracht10\Seo\Checks\Traits\ValidateResponse;

class ResponseCheck implements CheckInterface
{
    use ValidateResponse;

    public string $title = 'Check if the response is successful';

    public string $priority = 'high';

    public int $timeToFix = 10;

    public bool $checkSuccessful = false;

    public function handle(string $url, object $response): self
    {
        if ($this->validateResponse($response)) {
            $this->checkSuccessful = true;

            return $this;
        }

        $this->checkSuccessful = false;

        return $this;
    }
}
