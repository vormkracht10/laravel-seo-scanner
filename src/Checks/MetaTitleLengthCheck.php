<?php 

namespace Vormkracht10\Seo\Checks;

class MetaTitleLengthCheck implements CheckInterface
{
    public string $title = "Check if the title is not longer than 60 characters";

    public string $priority = 'medium';

    public int $timeToFix = 1;

    public bool $checkSuccessful = false;

    public function handle(string $url, string $response): self
    {
        $title = $this->getTitle($response);

        if (strlen($title) > 60) {
            $this->checkSuccessful = false;

            return $this;
        }

        $this->checkSuccessful = true;

        return $this;
    }

    private function getTitle(string $response): string|null
    {
        preg_match('/<title>(.*)<\/title>/', $response, $matches);

        return $matches[1] ?? null;
    }
}