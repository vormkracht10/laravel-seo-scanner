<?php

namespace Vormkracht10\Seo;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Vormkracht10\Seo\Commands\SeoCheck;
use Vormkracht10\Seo\Commands\SeoCheckUrl;

class SeoServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-seo')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_seo_score_columns')
            ->hasCommands([
                SeoCheck::class,
                SeoCheckUrl::class,
            ]);
    }
}
