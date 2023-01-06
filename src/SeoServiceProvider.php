<?php

namespace Vormkracht10\Seo;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
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
            ->hasTranslations()
            ->hasMigrations(['create_seo_scans_columns', 'create_seo_score_columns'])
            ->hasCommands([
                SeoCheck::class,
                SeoCheckUrl::class,
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToStarRepoOnGitHub('vormkracht10/laravel-seo');
            });
    }
}
