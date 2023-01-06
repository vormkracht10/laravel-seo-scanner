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
            ->hasMigration('create_seo_score_columns')
            ->hasCommands([
                SeoCheck::class,
                SeoCheckUrl::class,
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('vormkracht10/laravel-seo');
            });
    }
}
