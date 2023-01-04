<?php

// it('can run the SEO check for a single URL', function () {
//     $this->artisan('seo:check-url', ['url' => 'https://vormkracht10.nl'])
//         ->assertExitCode(0);
// });

// it('can only run configured checks for a single url', function () {
//     config('seo', [
//         'check_routes' => false,
//         'checks' => [
//             \Vormkracht10\Seo\Checks\Content\MultipleHeadingCheck::class,
//         ],
//         ...config('seo'),
//     ]);

//     $this->artisan('seo:check-url', ['url' => 'https://vormkracht10.nl'])
//         ->expectsOutputToContain('1 out of '.getCheckCount().' checks.')
//         ->assertExitCode(0);
// });

// it('can run all checks for a single url', function () {
//     config('seo', [
//         'check_routes' => false,
//         'checks' => [],
//         ...config('seo'),
//     ]);

//     $this->artisan('seo:check-url', ['url' => 'https://vormkracht10.nl'])
//         ->expectsOutputToContain(getCheckCount().' out of '.getCheckCount().' checks.')
//         ->assertExitCode(0);
// });

// it('can run the SEO check for routes', function () {
//     config('seo', [
//         'checks' => [
//             \Vormkracht10\Seo\Checks\Content\MultipleHeadingCheck::class,
//         ],
//         'routes' => [
//             'https://vormkracht10.nl',
//         ],
//         ...config('seo'),
//     ]);

//     $this->artisan('seo:check')
//         ->assertExitCode(0);
// });

// it('can only run configured checks', function () {
//     config('seo', [
//         'check_routes' => false,
//         'checks' => [
//             \Vormkracht10\Seo\Checks\Content\MultipleHeadingCheck::class,
//         ],
//         ...config('seo'),
//     ]);

//     $this->artisan('seo:check-url', ['url' => 'https://vormkracht10.nl'])
//         ->expectsOutputToContain('1 out of '.getCheckCount().' checks.')
//         ->assertExitCode(0);
// });
