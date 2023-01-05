# Check if your SEO is setup correctly in your Laravel application.


![GitHub release (latest by date)](https://img.shields.io/github/v/release/vormkracht10/laravel-seo)
[![Tests](https://github.com/vormkracht10/laravel-seo/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/vormkracht10/laravel-seo/actions/workflows/run-tests.yml)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/vormkracht10/laravel-seo)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/vormkracht10/laravel-seo.svg?style=flat-square)](https://packagist.org/packages/vormkracht10/laravel-seo)
[![Total Downloads](https://img.shields.io/packagist/dt/vormkracht10/laravel-seo.svg?style=flat-square)](https://packagist.org/packages/vormkracht10/laravel-seo)

![Screenshot 2023-01-05 at 14 14 59](https://user-images.githubusercontent.com/10845460/210788603-c86e78d0-95e8-44ec-9eee-4f05b5e6f55c.png)

## Introduction

Laravel SEO is a package that helps you to check if your SEO is setup correctly in your Laravel application. Besides just checking the SEO score of a page, it can also save the score to a model. This way you can check the SEO score of a specific page and show it in your application.

- [Installation](#installation)
- [Usage](#usage)
  * [Check the SEO score of routes](#check-the-seo-score-of-routes)
  * [Check the SEO score of a single page](#check-the-seo-score-of-a-single-page)
  * [Check the SEO score of a model](#check-the-seo-score-of-a-model)
  * [Saving SEO scores into the database](#saving-seo-scores-into-the-database)
- [Available checks](#available-checks)
  * [Configuration](#configuration)
  * [Content](#content)
  * [Meta](#meta)
  * [Performance](#performance)
- [Testing](#testing)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [Security Vulnerabilities](#security-vulnerabilities)
- [Credits](#credits)
- [License](#license)

## Installation

You can install the package via composer:

```bash
composer require vormkracht10/laravel-seo
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="seo-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="seo-config"
```

This is the contents of the published config file:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Check classes
    |--------------------------------------------------------------------------
    |
    | The following array lists the "check" classes that will be registered
    | with Laravel Seo. These checks run an check on the application via
    | various methods. Feel free to customize it.
    |
    | An example of a check class:
    | \Vormkracht10\Seo\Checks\Content\BrokenLinkCheck::class
    |
    */
    'checks' => ['*'],

    // If you wish to skip running some checks, list the classes in the array below.
    'exclude_checks' => [],

    /*
    |--------------------------------------------------------------------------
    | Check paths
    |--------------------------------------------------------------------------
    |
    | The following array lists the "checks" paths that will be searched
    | recursively to find check classes. This option will only be used
    | if the checks option above is set to the asterisk wildcard. The
    | key is the base namespace to resolve the class name.
    |
    */
    'check_paths' => [
        'Vormkracht10\\Seo\\Checks' => base_path('vendor/vormkracht10/laravel-seo/src/Checks'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    |
    | The following array lists the "checkable" routes that will be registered
    | with Laravel Seo. These routes will be checked for SEO. Feel free to
    | customize it. To check for specific routes, use the route name.
    |
    | An example of a checkable route:
    | 'blog.index'
    |
    */
    'check_routes' => true,
    'routes' => ['*'],

    // If you wish to skip running some checks on some routes, list the routes
    // in the array below by using the route name. For example:
    // 'blog.index'
    'exclude_routes' => [],

    // If you wish to skip running some checks on some paths, list the paths
    // in the array below.
    'exclude_paths' => [
        'admin/*',
        'nova/*',
        'horizon/*',
        'vapor-ui/*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    |
    | Here you can specify the database connection and table name that will be
    | used to save the SEO scores. When you set the save option to true, the
    | SEO score will be saved to the database.
    |
    */
    'database' => [
        'connection' => 'mysql',
        'table_name' => 'seo_scores',
        'save' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Here you can specify which models you want to check. When you specify a
    | model, the SEO score will be saved to the database. This way you can
    | check the SEO score of a specific page.
    |
    | An example of a model:
    | \App\Models\BlogPost::class
    |
    */
    'models' => [],
];
```

## Usage

### Check the SEO score of routes

By default, all `GET` routes will be checked for SEO. If you want to check the SEO score of a specific route, you can add the route name to the `routes` array in the config file. If you want to skip a route, you can add the route name to the `exclude_routes` array in the config file. If you don't want to check the SEO score of routes at all, you can set the `check_routes` option to `false` in the config file.

To check the SEO score of your routes, run the following command:

```bash
php artisan seo:check-routes
```

### Check the SEO score of a single page
Want to get the score of a specific url? Run the following command:

```bash
php artisan seo:check-url https://vormkracht10.nl
```

> Note: The command will only check the SEO score of the url and output the score in the CLI. It will not save the score to the database.

### Check the SEO score of a model

When you have an application where you have a lot of pages which are related to a model, you can save the SEO score to the model. This way you can check the SEO score of a specific page and show it in your application. 

For example, you have a `BlogPost` model which has a page for each content item:

1. Add the model to the `models` array in the config file. 
2. Implement the `SeoInterface` in your model.
3. Add the `HasSeoScore` trait to your model. 

> Note: Please make sure that the model has a `url` attribute. This attribute will be used to check the SEO score of the model. Also check that the migrations are run. Otherwise the command will fail.

```php

use Vormkracht10\Seo\Traits\HasSeoScore;
use Vormkracht10\Seo\SeoInterface;

class BlogPost extends Model implements SeoInterface
{
    use HasFactory,
        HasSeoScore;

    protected $fillable = [
        'title',
        'description',
        'slug',
        // ...
    ];

    public function getUrlAttribute(): string
    {
        return 'https://vormkracht10.nl/' . $this->slug;
    }
}
```

You can get the SEO score of a model by calling the `seoScore()` or `seoScoreDetails()` methods on the model. These methods are defined in the `HasSeoScore` trait and can be overridden by adding the modified method in your model. 

To fill the database with the scores of all models, run the following command:

```bash
php artisan seo:check
```

To get the SEO score(s) of a model, you have the following options: 

1. Get the SEO scores of a single model from the database:

```php
$scores = Model::withSeoScores()->get();
```

2. Run a SEO score check on a single model:

```php
$model = Model::first();

// Get just the score
$score = $model->getCurrentScore();

// Get the score including the details
$scoreDetails = $model->getCurrentScoreDetails();
```

### Saving SEO scores into the database
    
When you want to save the SEO score to the database, you need to set the `save` option to `true` in the config file. 

```php
'database' => [
    'connection' => 'mysql',
    'table_name' => 'seo_scores',
    'save' => true,
],
```

Optionally you can specify the table name and database connection in the config file. If you want to save the SEO score to a model, you need to add the model to the `models` array in the config file. More information about this can be found in the [Check the SEO score of a model](#check-the-seo-score-of-a-model) section.

## Available checks
These checks are available in the package. You can add or remove checks in the config file. These checks are based on SEO best practices and if all checks are green, your website will have a good SEO score. If you want to add more checks, you can create a pull request.

### Configuration
✅ The page does not have 'noindex' set. <br>
✅ The page does not have 'nofollow' set. <br>
✅ Robots.txt allows indexing. <br>

### Content
✅ The page has an H1 tag and if it is used only once per page. <br>
✅ All links redirect to an url using HTTPS. <br>
✅ Every image has an alt tag. <br>
✅ The page contains no broken links. <br>
✅ The page contains no broken images. <br>
✅ Length of the content is at least 2100 characters. <br>

### Meta
✅ The page has a meta description. <br>
✅ The page title is not longer than 60 characters. <br>
✅ The page title does not contain 'home' or 'homepage'. <br>
✅ The page has an Open Graph image.<br>
✅ The lang attribute is set on the html tag.<br>

### Performance
✅ Time To First Byte (TTFB) is below 600ms. <br>
✅ The page response returns a 200 status code. <br>
✅ HTML is not larger than 100 KB. <br>
✅ Images are not larger than 1 MB. <br>
✅ JavaScript files are not larger than 1 MB. <br>
✅ CSS files are not larger than 15 KB. <br>
✅ HTML is GZIP compressed. <br>

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Bas van Dinther](https://github.com/Baspa)
-   [Mark van Eijk](https://github.com/markvaneijk)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
