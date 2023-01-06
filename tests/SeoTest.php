<?php

it('can run the SEO check for a single URL', function () {
    $this->artisan('seo:scan-url', ['url' => 'https://vormkracht10.nl'])
        ->assertExitCode(0);
});

it('can only run configured checks for a single url', function () {
    config('seo', [
        'check_routes' => false,
        'checks' => [
            \Vormkracht10\Seo\Checks\Content\MultipleHeadingCheck::class,
        ],
    ]);

    $this->artisan('seo:scan-url', ['url' => 'https://vormkracht10.nl'])
        ->expectsOutputToContain('1 out of '.getCheckCount().' checks.')
        ->assertExitCode(0);
});

it('can run all checks for a single url', function () {
    config('seo', [
        'check_routes' => false,
        'database' => [
            'save' => false,
        ],
        'checks' => [],
    ]);

    $this->artisan('seo:scan-url', ['url' => 'https://vormkracht10.nl'])
        ->expectsOutputToContain(getCheckCount().' out of '.getCheckCount().' checks.')
        ->assertExitCode(0);
});

it('can run the SEO check for routes', function () {
    config('seo', [
        'checks' => [
            \Vormkracht10\Seo\Checks\Content\MultipleHeadingCheck::class,
        ],
        'routes' => [
            'https://vormkracht10.nl',
        ],
    ]);

    config(['seo.database.save' => false]);

    $this->artisan('seo:scan')
        ->assertExitCode(0);
});

it('can only run configured checks', function () {
    config('seo', [
        'check_routes' => false,
        'checks' => [
            \Vormkracht10\Seo\Checks\Content\MultipleHeadingCheck::class,
        ],
    ]);

    config(['seo.database.save' => false]);

    $this->artisan('seo:scan-url', ['url' => 'https://vormkracht10.nl'])
        ->expectsOutputToContain('1 out of '.getCheckCount().' checks.')
        ->assertExitCode(0);
});
