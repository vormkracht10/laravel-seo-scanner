<?php 

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\Seo\Checks\Meta\DescriptionCheck;

it('can perform the description check on a page with a description', function () {
    $check = new DescriptionCheck ();
    $crawler = new Crawler ();

    Http::fake([
        'vormkracht10.nl' => Http::response('<html><head><meta name="description" content="Vormkracht10 is a web development agency based in Amsterdam."></head><body></body></html>', 200),
    ]);

    $crawler->addHtmlContent(Http::get( 'vormkracht10.nl' )->body());

    $this->assertTrue($check->check(Http::get('vormkracht10.nl' ), $crawler));
});

it('can perform the description check on a page without a description', function () {
    $check = new DescriptionCheck ();
    $crawler = new Crawler ();

    Http::fake([
        'vormkracht10.nl' => Http::response('<html><head></head><body></body></html>', 200),
    ]);

    $crawler->addHtmlContent(Http::get( 'vormkracht10.nl' )->body());

    $this->assertFalse($check->check(Http::get('vormkracht10.nl' ), $crawler));
});