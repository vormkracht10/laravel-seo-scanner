<?php

use Backstage\Seo\Services\UrlDiscoverer;
use Illuminate\Support\Facades\Http;

function urlset(array $urls): string
{
    $locations = collect($urls)->map(fn ($url) => "<url><loc>{$url}</loc></url>")->implode('');

    return '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.$locations.'</urlset>';
}

it('discovers urls from the sitemap listed in robots.txt', function () {
    Http::fake([
        'https://example.com/robots.txt' => Http::response("User-agent: *\nSitemap: https://example.com/custom-sitemap.xml"),
        'https://example.com/custom-sitemap.xml' => Http::response(urlset([
            'https://example.com/',
            'https://example.com/about',
        ])),
    ]);

    $urls = app(UrlDiscoverer::class)->discover('example.com');

    expect($urls->all())->toBe([
        'https://example.com/',
        'https://example.com/about',
    ]);
});

it('falls back to the sitemap.xml convention when robots.txt lists no sitemap', function () {
    Http::fake([
        'https://example.com/robots.txt' => Http::response('User-agent: *'),
        'https://example.com/sitemap.xml' => Http::response(urlset(['https://example.com/contact'])),
    ]);

    $urls = app(UrlDiscoverer::class)->discover('example.com');

    expect($urls->all())->toBe(['https://example.com/contact']);
});

it('follows sitemap index files', function () {
    Http::fake([
        'https://example.com/robots.txt' => Http::response('User-agent: *'),
        'https://example.com/sitemap.xml' => Http::response(
            '<?xml version="1.0"?><sitemapindex><sitemap><loc>https://example.com/pages.xml</loc></sitemap></sitemapindex>'
        ),
        'https://example.com/pages.xml' => Http::response(urlset(['https://example.com/pricing'])),
    ]);

    $urls = app(UrlDiscoverer::class)->discover('example.com');

    expect($urls->all())->toBe(['https://example.com/pricing']);
});

it('excludes urls on other hosts and urls to assets', function () {
    Http::fake([
        'https://example.com/robots.txt' => Http::response('User-agent: *'),
        'https://example.com/sitemap.xml' => Http::response(urlset([
            'https://example.com/page',
            'https://www.example.com/www-page',
            'https://other.com/page',
            'https://example.com/brochure.pdf',
        ])),
    ]);

    $urls = app(UrlDiscoverer::class)->discover('example.com');

    expect($urls->all())->toBe([
        'https://example.com/page',
        'https://www.example.com/www-page',
    ]);
});

it('caps the discovered urls at the max pages limit', function () {
    Http::fake([
        'https://example.com/robots.txt' => Http::response('User-agent: *'),
        'https://example.com/sitemap.xml' => Http::response(urlset(
            collect(range(1, 10))->map(fn ($i) => "https://example.com/page-{$i}")->all()
        )),
    ]);

    $urls = app(UrlDiscoverer::class)->discover('example.com', maxPages: 3);

    expect($urls)->toHaveCount(3);
});

it('crawls same-host links when no sitemap exists', function () {
    Http::fake([
        'https://example.com/robots.txt' => Http::response('Not found', 404),
        'https://example.com/sitemap.xml' => Http::response('Not found', 404),
        'https://example.com/' => Http::response(
            '<html><body><a href="/about">About</a><a href="https://other.com/">Other</a></body></html>',
            headers: ['Content-Type' => 'text/html'],
        ),
        'https://example.com/about' => Http::response(
            '<html><body><a href="/contact">Contact</a></body></html>',
            headers: ['Content-Type' => 'text/html'],
        ),
        'https://example.com/contact' => Http::response(
            '<html><body></body></html>',
            headers: ['Content-Type' => 'text/html'],
        ),
    ]);

    $urls = app(UrlDiscoverer::class)->discover('example.com');

    expect($urls->all())->toBe([
        'https://example.com/',
        'https://example.com/about',
        'https://example.com/contact',
    ]);
});

it('falls back to the base url when nothing can be discovered', function () {
    Http::fake([
        '*' => Http::response('Not found', 404),
    ]);

    $urls = app(UrlDiscoverer::class)->discover('example.com');

    expect($urls->all())->toBe(['https://example.com']);
});

it('normalizes domain input into a base url', function () {
    $discoverer = app(UrlDiscoverer::class);

    expect($discoverer->normalizeBaseUrl('example.com'))->toBe('https://example.com')
        ->and($discoverer->normalizeBaseUrl('http://example.com/some/path'))->toBe('http://example.com')
        ->and($discoverer->normalizeBaseUrl('https://example.com:8080/'))->toBe('https://example.com:8080');
});

it('rejects input without a valid host', function () {
    app(UrlDiscoverer::class)->normalizeBaseUrl('https://');
})->throws(InvalidArgumentException::class);
