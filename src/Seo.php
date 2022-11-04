<?php

namespace Vormkracht10\Seo;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Vormkracht10\Seo\Checks\CheckEnum;
use Vormkracht10\Seo\Checks\MetaTitleCheck;
use Vormkracht10\Seo\Checks\MetaTitleLengthCheck;
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

        $this->runChecks(url: $url, response: $response);

        return (new SeoScore)($this->successful, $this->failed);
    }

    private function visitPage(string $url): object
    {
        $response = $this->http::get(url: $url);

        return $response;
    }

    private function runChecks(string $url, object $response): void
    {
        $checks = app(Pipeline::class)
            ->send($response)
            ->through([
                ResponseCheck::class,
                MetaTitleCheck::class,
                MetaTitleLengthCheck::class,
            ])
            ->thenReturn();

        $checks = collect($checks['checks']);

        $this->successful = $checks->filter(fn ($check) => $check->checkSuccessful);
        $this->failed = $checks->filter(fn ($check) => ! $check->checkSuccessful);
    }
}
