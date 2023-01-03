<?php

it('can run the SEO check for a single URL', function () {
    $this->artisan('seo:check-url', ['url' => 'https://vormkracht10.nl'])
        ->assertExitCode(0);
});