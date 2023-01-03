<?php 

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Checks\Meta\OpenGraphImageCheck;

it('can perform open graph image check on a page with a broken open graph image', function () {
    $check = new OpenGraphImageCheck ();
    $crawler = new Crawler ();

    Http::fake([
        'vormkracht10.nl' => Http::response('<html><head><meta property="og:image" content="https://vormkracht10.nl/images/og-image.png"></head><body></body></html>', 200),
    ]);

    $crawler->addHtmlContent(Http::get( 'vormkracht10.nl' )->body());

    $this->assertFalse($check->check(Http::get('vormkracht10.nl' ), $crawler));
});

it('can perform open graph image check on a page without an open graph image', function () {
    $check = new OpenGraphImageCheck ();
    $crawler = new Crawler ();

    Http::fake([
        'vormkracht10.nl' => Http::response('<html><head></head><body></body></html>', 200),
    ]);

    $crawler->addHtmlContent(Http::get( 'vormkracht10.nl' )->body());

    $this->assertFalse($check->check(Http::get('vormkracht10.nl' ), $crawler));
});

it('can perform open graph image check on a page with a working open graph image', function () {
    $check = new OpenGraphImageCheck ();
    $crawler = new Crawler ();

    Http::fake([
        'vormkracht10.nl' => Http::response('<html><head><meta property="og:image" content="https://source.unsplash.com/random"></head><body></body></html>', 200),
    ]);

    $crawler->addHtmlContent(Http::get( 'vormkracht10.nl' )->body());

    $this->assertTrue($check->check(Http::get('vormkracht10.nl' ), $crawler));
});