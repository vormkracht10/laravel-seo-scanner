<?php

namespace Vormkracht10\Seo;

use Vormkracht10\Seo\SeoScore;
use Illuminate\Support\Facades\Http;
use Vormkracht10\Seo\Checks\CheckEnum;

class Seo
{
    public function __construct(
        protected Http $http,
        protected array $success = [],
        protected array $failed = [],
    ){}

    public function check(string $url): SeoScore
    {
        $response = $this->visitPage(url: $url);

        $this->runChecks(url: $url, response: $response);
        
        return (new SeoScore(success: $this->success, failed: $this->failed));
    }

    private function visitPage(string $url): object
    {
        $response = $this->http::get(url: $url);

        return $response;
    }

    private function runChecks(string $url, object $response): void
    {
        foreach(CheckEnum::cases() as $check) {

            $check = new $check->value();
            $check->handle(url: $url, response: $response);

            if ($check->checkSuccessful) {
                $this->success[$url] = $check;

                continue;
            }

            $this->failed[$url] = $check;
        }
    }
}
