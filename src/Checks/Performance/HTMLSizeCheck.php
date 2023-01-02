<?php

namespace Vormkracht10\Seo\Checks\Performance;

use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\Check;
use Vormkracht10\Seo\Traits\PerformCheck;

class HTMLSizeCheck implements Check
{
    use PerformCheck;

    public string $title = 'HTML is not larger than 100 KB';

    public string $priority = 'medium';

    public int $timeToFix = 60;

    public int $scoreWeight = 5;

    public bool $continueAfterFailure = true;

    public string|null $failureReason;

    public array|int|string|null $actualValue = null;

    public int|null|string $expectedValue = 100000;

    public function check(Response $response): bool
    {
        $this->expectedValue = bytesToHumanReadable($this->expectedValue);

        $content = $this->getContentToValidate($response);

        if (! $content || ! $this->validateContent($content)) {
            return false;
        }

        return true;
    }

    public function getContentToValidate(Response $response): string|array|null
    {
        return $response->body();
    }

    public function validateContent(string|array $content): bool
    {
        if (strlen($content) > 100000) {

            $this->actualValue = strlen($content);

            $this->failureReason = __('failed.performance.html_size', [
                'actualValue' => bytesToHumanReadable($this->actualValue),
                'expectedValue' => $this->expectedValue,
            ]);

            return false;
        }
        return strlen($content) < 100000;
    }
}
