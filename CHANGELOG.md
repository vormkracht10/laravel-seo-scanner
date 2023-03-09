# Changelog

All notable changes to `laravel-seo` will be documented in this file.

## v1.2.0 - 2023-03-09

### What's Changed

- Adding a new config to disable SSL certificate integrity check by @MuriloChianfa in https://github.com/vormkracht10/laravel-seo-scanner/pull/18
- Bump dependabot/fetch-metadata from 1.3.5 to 1.3.6 by @dependabot in https://github.com/vormkracht10/laravel-seo-scanner/pull/20
- [Feature] Laravel 10 compatibility by @Baspa in https://github.com/vormkracht10/laravel-seo-scanner/pull/24

### New Contributors

- @MuriloChianfa made their first contribution in https://github.com/vormkracht10/laravel-seo-scanner/pull/18

**Full Changelog**: https://github.com/vormkracht10/laravel-seo-scanner/compare/v1.1.0...v1.2.0

## v1.1.0 - 2023-01-13

### What's Changed

- Fix Windows tests by removing SSL validation when using `curl()` by @Baspa in https://github.com/vormkracht10/laravel-seo-scanner/pull/16
- Check if a relative url is used when performing broken link checks by @Baspa in https://github.com/vormkracht10/laravel-seo-scanner/pull/17

**Full Changelog**: https://github.com/vormkracht10/laravel-seo-scanner/compare/v1.0.1...v1.1.0

## v1.0.0 - 2023-01-06

### Laravel SEO Scanner v1.0.0 released

🚀 Vormkracht10 proudly presents the first release of the Laravel SEO Scanner. This release includes basic features to get started scanning your Laravel application routes for SEO improvements. With this release we have a solid basement to build upon further.

#### Features

- Total of 21 checks
- Extensive config file where you can configure how the package behaves.
- Command to check all configured routes
- Command to check a single url
- Possibility to save results into the database
- Corresponding models to easily retrieve the data

**Full Changelog**: https://github.com/vormkracht10/laravel-seo-scanner/commits/v1.0.0
