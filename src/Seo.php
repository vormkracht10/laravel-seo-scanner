<?php

namespace Vormkracht10\Seo;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Vormkracht10\Seo\Checks\Meta\DescriptionCheck;
use Vormkracht10\Seo\Checks\Meta\TitleCheck;
use Vormkracht10\Seo\Checks\Meta\TitleLengthCheck;
use Vormkracht10\Seo\Checks\ResponseCheck;

class Seo
{
    public function __construct(
        protected Http $http,
        protected Collection $successful,
        protected Collection $failed,
    ) {
    }

    public function check(string $url): SeoScore
    {
        $response = $this->visitPage(url: $url);

        $this->runChecks(response: $response);

        return (new SeoScore)($this->successful, $this->failed);
    }

    private function visitPage(string $url): object
    {
        $response = $this->http::get(url: $url);

        return $response;
    }

    private function runChecks(object $response): void
    {
        $checks = app(Pipeline::class)
            ->send($response)
            ->through([
                ResponseCheck::class,
                TitleCheck::class,
                TitleLengthCheck::class,
                DescriptionCheck::class,
            ])
            ->thenReturn();

        $checks = collect($checks['checks']);

        $this->successful = $checks->filter(fn ($check) => $check->checkSuccessful);
        $this->failed = $checks->filter(fn ($check) => ! $check->checkSuccessful);
    }
}
