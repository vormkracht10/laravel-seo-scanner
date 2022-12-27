# Check if your SEO is setup correctly in your Laravel application.


![GitHub release (latest by date)](https://img.shields.io/github/v/release/vormkracht10/laravel-seo)
[![Tests](https://github.com/vormkracht10/laravel-seo/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/vormkracht10/laravel-seo/actions/workflows/run-tests.yml)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/vormkracht10/laravel-seo)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/vormkracht10/laravel-seo.svg?style=flat-square)](https://packagist.org/packages/vormkracht10/laravel-seo)
[![Total Downloads](https://img.shields.io/packagist/dt/vormkracht10/laravel-seo.svg?style=flat-square)](https://packagist.org/packages/vormkracht10/laravel-seo)

Laravel SEO is a package that helps you to check if your SEO is setup correctly in your Laravel application. Besides just checking the SEO score of a page, it can also save the score to a model. This way you can check the SEO score of a specific page and show it in your application.

- [Installation](#installation)
- [Usage](#usage)
- [Available checks](#available-checks)
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

## Available checks
These checks are available in the package. You can add or remove checks in the config file. These checks are based on SEO best practices and if all checks are green, your website will have a good SEO score. If you want to add more checks, you can create a pull request.

### Content

✅ Check if the page has a H1 tag and if it is used only once per page. <br>
✅ Check if all links redirect to a HTTPS url. <br>
✅ Check if every image has an alt tag. <br>
✅ Check if the page contains no broken links. <br>
✅ Check if the images are not larger than 1 MB. <br>
✅ Check if the HTML is not larger than 100 KB. <br>

### Meta

✅ Check if the page has a meta description. <br>
✅ Check if the page title is not longer than 60 characters. <br>
✅ Check if the page title does not contain 'home' or 'homepage'. <br>

### Performance

✅ Check if Time To First Byte (TTFB) is below 600ms. <br>
✅ Check if the response returns a 200 status code. <br>

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
