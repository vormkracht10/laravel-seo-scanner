<?php

use Backstage\Seo\Checks\Content\MultipleHeadingCheck;
use Backstage\Seo\Jobs\ScanChunk;
use Backstage\Seo\Services\DomainScanner;
use Backstage\Seo\Services\PageScanRunner;
use Backstage\Seo\Tests\Support\Product;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    config(['seo.database.connection' => 'testing']);
    config(['seo.database.save' => true]);
    config(['seo.checks' => [MultipleHeadingCheck::class]]);

    $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

    Schema::create('products', function ($table) {
        $table->bigIncrements('id');
        $table->string('url')->nullable();
    });

    Http::fake([
        'https://example.com/robots.txt' => Http::response('User-agent: *'),
        'https://example.com/sitemap.xml' => Http::response(
            '<?xml version="1.0"?><urlset><url><loc>https://example.com/</loc></url><url><loc>https://example.com/about</loc></url></urlset>'
        ),
        '*' => Http::response('<html><head><title>Test</title></head><body><h1>Test</h1></body></html>'),
    ]);
});

afterEach(function () {
    Schema::dropIfExists('products');
});

it('scans every discovered page of a domain and finalizes the scan', function () {
    $scan = app(DomainScanner::class)->scan('example.com');

    expect($scan->url)->toBe('https://example.com')
        ->and($scan->pages)->toBe(2)
        ->and($scan->finished_at)->not->toBeNull();

    $urls = DB::connection('testing')->table('seo_scores')->pluck('url');

    expect($urls->all())->toBe(['https://example.com/', 'https://example.com/about']);
});

it('relates the scan and its scores to the subject model', function () {
    $product = Product::create(['url' => 'https://example.com']);

    $scan = app(DomainScanner::class)->scan('example.com', subject: $product);

    expect($scan->model_type)->toBe($product->getMorphClass())
        ->and((int) $scan->model_id)->toBe($product->id);

    $row = DB::connection('testing')->table('seo_scores')->first();

    expect($row->model_type)->toBe($product->getMorphClass())
        ->and((int) $row->model_id)->toBe($product->id);
});

it('dispatches the page scans as a job batch when queueing', function () {
    Bus::fake();

    $scan = app(DomainScanner::class)->scan('example.com', queue: true);

    expect($scan->url)->toBe('https://example.com')
        ->and($scan->finished_at)->toBeNull();

    Bus::assertBatched(function ($batch) use ($scan) {
        return $batch->jobs->count() === 1
            && $batch->jobs->first() instanceof ScanChunk
            && $batch->jobs->first()->scanId === $scan->id
            && $batch->jobs->first()->urls === ['https://example.com/', 'https://example.com/about'];
    });
});

it('lets queued chunk jobs inherit the subject from the scan', function () {
    Bus::fake();

    $product = Product::create(['url' => 'https://example.com']);

    $scan = app(DomainScanner::class)->scan('example.com', subject: $product, queue: true);

    // Run the chunk job that the batch would process.
    (new ScanChunk(scanId: $scan->id, urls: ['https://example.com/']))
        ->handle(app(PageScanRunner::class));

    $row = DB::connection('testing')->table('seo_scores')->first();

    expect($row->model_type)->toBe($product->getMorphClass())
        ->and((int) $row->model_id)->toBe($product->id);
});
