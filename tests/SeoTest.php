<?php

it('can run the SEO check for a single URL', function () {
    $this->artisan('seo:scan-url', ['url' => 'https://vormkracht10.nl'])
        ->assertExitCode(0);
});

it('can run the SEO check for routes', function () {
    config(['seo.database.save' => false]);
    config(['seo.routes' => [
        'https://vormkracht10.nl',
    ]]);
    config(['seo.checks' => [
        \Vormkracht10\Seo\Checks\Content\MultipleHeadingCheck::class,
    ]]);

    $this->artisan('seo:scan')
        ->assertExitCode(0);
});

it('can only run configured checks', function () {
    config(['seo.database.save' => false]);
    config(['seo.check_routes' => false]);
    config(['seo.checks' => [
        \Vormkracht10\Seo\Checks\Content\MultipleHeadingCheck::class,
    ]]);

    $this->artisan('seo:scan-url', ['url' => 'https://vormkracht10.nl'])
        ->expectsOutputToContain('1 out of '.getCheckCount().' checks.')
        ->assertExitCode(0);
});
