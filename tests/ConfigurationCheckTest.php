<?php

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Checks\Configuration\NoFollowCheck;
use Vormkracht10\Seo\Checks\Configuration\NoIndexCheck;
use Vormkracht10\Seo\Checks\Configuration\RobotsCheck;

it('can perform the nofollow check', function () {
    $check = new NoFollowCheck();

    Http::fake([
        'vormkracht10.nl' => Http::response('', 200, ['X-Robots-Tag' => 'nofollow']),
    ]);

    $this->assertFalse($check->check(Http::get('vormkracht10.nl'), new Crawler()));

    Http::fake([
        'vormkracht10.nl' => Http::response('<html><head><meta name="robots" content="nofollow"></head></html>', 200),
    ]);

    $this->assertFalse($check->check(Http::get('vormkracht10.nl'), new Crawler()));

    Http::fake([
        'vormkracht10.nl' => Http::response('<html><head><meta name="googlebot" content="nofollow"></head></html>', 200),
    ]);

    $this->assertFalse($check->check(Http::get('vormkracht10.nl'), new Crawler()));
});

it('can perform the noindex check', function () {
    $check = new NoIndexCheck();

    Http::fake([
        'vormkracht10.nl' => Http::response('', 200, ['X-Robots-Tag' => 'noindex']),
    ]);

    $this->assertFalse($check->check(Http::get('vormkracht10.nl'), new Crawler()));

    Http::fake([
        'vormkracht10.nl' => Http::response('<html><head><meta name="robots" content="noindex"></head></html>', 200),
    ]);

    $this->assertFalse($check->check(Http::get('vormkracht10.nl'), new Crawler()));

    Http::fake([
        'vormkracht10.nl' => Http::response('<html><head><meta name="googlebot" content="noindex"></head></html>', 200),
    ]);

    $this->assertFalse($check->check(Http::get('vormkracht10.nl'), new Crawler()));
});

it('can perform the robots check', function () {
    $check = new RobotsCheck();

    Http::fake([
        'vormkracht10.nl/robots.txt' => Http::response('User-agent: Googlebot
            Disallow: /admin', 200),
    ]);

    $this->assertTrue($check->check(Http::get('vormkracht10.nl'), new Crawler()));
});
