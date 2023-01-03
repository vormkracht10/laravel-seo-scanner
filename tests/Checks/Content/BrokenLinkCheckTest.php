<?php

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Checks\Content\BrokenLinkCheck;

it('can perform the broken link check on broken links', function () {
    $check = new BrokenLinkCheck();
    $crawler = new Crawler();

    Http::fake([
        'vormkracht10.nl' => Http::response('<html><head></head><body><a href="https://vormkracht10.nl/404">Vormkracht10</a></body></html>', 200),
    ]);

    $crawler->addHtmlContent(Http::get('vormkracht10.nl')->body());

    $this->assertFalse($check->check(Http::get('vormkracht10.nl'), $crawler));
});

it('can perform the broken link check on working links', function () {
    $check = new BrokenLinkCheck();
    $crawler = new Crawler();

    Http::fake([
        'vormkracht10.nl' => Http::response('<html><head></head><body><a href="https://vormkracht10.nl">Vormkracht10</a></body></html>', 200),
    ]);

    $crawler->addHtmlContent(Http::get('vormkracht10.nl')->body());

    $this->assertTrue($check->check(Http::get('vormkracht10.nl'), $crawler));
});

it('can perform the broken link check on content where no links are used', function () {
    $check = new BrokenLinkCheck();
    $crawler = new Crawler();

    Http::fake([
        'vormkracht10.nl' => Http::response('<html><head></head><body></body></html>', 200),
    ]);

    $crawler->addHtmlContent(Http::get('vormkracht10.nl')->body());

    $this->assertTrue($check->check(Http::get('vormkracht10.nl'), $crawler));
});
