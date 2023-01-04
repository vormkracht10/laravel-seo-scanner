<?php

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Checks\Performance\TTFBCheck;

it('can perform the ttfb check', function () {
    $check = new TTFBCheck();

    Http::fake([
        'vormkracht10.nl/robots.txt' => Http::response('<html></html>', 200),
    ]);

    if ($check->check(Http::get('vormkracht10.nl'), new Crawler()) && $check->actualValue > $check->expectedValue) {
        $this->assertFalse($check->check(Http::get('vormkracht10.nl'), new Crawler()));
    } else {
        $this->assertTrue($check->check(Http::get('vormkracht10.nl'), new Crawler()));
    }
});
