<?php

namespace Vormkracht10\Seo;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Vormkracht10\Seo\Checks\CheckEnum;

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
        foreach (CheckEnum::values() as $check) {
            $check = new $check;
            $check->handle(url: $url, response: $response);

            if ($check->checkSuccessful) {
                $this->successful->put($url, $check);

                continue;
            }

            $this->failed->put($url, $check);
        }
    }
}
