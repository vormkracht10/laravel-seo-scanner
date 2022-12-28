<?php

it('can run the SEO check for a single URL', function () {
    $this->artisan('seo:check-url', ['url' => 'https://vormkracht10.nl'])
        ->assertExitCode(0);
});

it('can run the SEO check for routes', function () {
    config('seo', [
        'routes' => [
            'https://vormkracht10.nl',
        ],
        ...config('seo'),
    ]);

    $this->artisan('seo:check')
        ->assertExitCode(0);
});
