<?php

it('can run the SEO check for a single URL', function () {
    // TODO: Probably we can't run this from the test suite, because we don't have 'artisan' available.
    $this->artisan('seo:check-url', ['url' => 'https://vormkracht10.nl'])
        ->expectsOutput('Done!')
        ->assertExitCode(0);
});
