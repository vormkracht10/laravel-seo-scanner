# Check if your SEO is setup correctly in your Laravel application.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/vormkracht10/laravel-seo.svg?style=flat-square)](https://packagist.org/packages/vormkracht10/laravel-seo)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/vormkracht10/laravel-seo/run-tests?label=tests)](https://github.com/vormkracht10/laravel-seo/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/vormkracht10/laravel-seo/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/vormkracht10/laravel-seo/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/vormkracht10/laravel-seo.svg?style=flat-square)](https://packagist.org/packages/vormkracht10/laravel-seo)

Laravel SEO is a package that helps you to check if your SEO is setup correctly in your Laravel application. Besides just checking the SEO score of a page, it can also save the score to a model. This way you can check the SEO score of a specific page and show it in your application.

- [Installation](#installation)
- [Usage](#usage)
- [List of checks](#list-of-checks)
  * [Performance](#performance)
  * [Content](#content)
  * [Meta](#meta)
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
    | Check pages and models
    |--------------------------------------------------------------------------
    |
    | Here you can specify which pages you want to check. It is possible to
    | specify a model which implements the SeoScore interface. This way you
    | can check the SEO score of a specific page.
    |
    */
    'pages' => [
        'model' => '',
    ],


    /*
    |--------------------------------------------------------------------------
    | Check classes
    |--------------------------------------------------------------------------
    |
    | The following array lists the "check" classes that will be registered
    | with Laravel Seo. These checks run an check on the application via
    | various methods. Feel free to customize it.
    |
    */
    'checks' => ['*'],

    // If you wish to skip running some analyzers, list the classes in the array below.
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
];

```

## Usage

Implement the SeoInterface in your model and make sure to add the needed methods to your model.

> Note: Please make sure that the seo_score column is added to the fillable array in your model. Otherwise the score will not be saved.

```php
class Content extends Model implements SeoInterface
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'path',
        'seo_score',
    ];

    public function seoScore(): SeoScore
    {
        return Seo::check(url: $this->url);
    }

    public function getScore(): int
    {
        return $this->seoScore()->getScore();
    }

    // Optional, but make sure you can pass the url of the model to the Seo facade.
    protected function getUrlAttribute()
    {
        return 'https://vormkracht10.nl/' . $this->path;
    }
}
```

You can get the SEO score of a model by calling the `seoScore()` method on the model, as seen in the example above. Do you want to get the scores of all models? Run the following command:

```bash
php artisan seo:check
```

Want to get the score of a specific url? Run the following command:

```bash
php artisan seo:check-url https://vormkracht10.nl
```

## List of checks

### Performance

- Check if Time To First Byte (TTFB) is below 600ms.
- Check if the response returns a 200 status code.

### Content

- Check if the page has a H1 tag and if it is used only once per page.
- Check if all links redirect to a HTTPS url.
- Check if every image has an alt tag.
- Check if the page contains no broken links.

### Meta
- Check if the page has a meta description.
- Check if the page title is not longer than 60 characters.
- Check if the page title does not contain 'home' or 'homepage'.

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

-   [Bas van Dinther](https://github.com/vormkracht10)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
