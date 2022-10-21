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
        $response = $this->visitPage($url);

        $this->runChecks($url, $response);
        
        return (new SeoScore($this->success, $this->failed));
    }

    private function visitPage(string $url): string
    {
        $response = $this->http::get($url);

        return $response->body();
    }

    private function runChecks(string $url, string $response): void
    {
        foreach(CheckEnum::cases() as $check) {

            $check = new $check->value();
            $check->handle($url, $response);

            if ($check->checkSuccessful) {
                $this->success[] = $check;

                continue;
            }

            $this->failed[] = $check;
        }
    }
}
