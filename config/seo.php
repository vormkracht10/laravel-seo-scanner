<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | The following array lists the cache options for the application.
    |
    */
    'cache' => [
        // Only drivers that support tags are supported.
        // These are: array, memcached and redis.
        'driver' => 'array',
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
        'Vormkracht10\\Seo\\Checks' => base_path('vendor/vormkracht10/laravel-seo-scanner/src/Checks'),
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
    | Domains (DNS resolving)
    |--------------------------------------------------------------------------
    |
    | Here you can add domains and a corresponding IP address
    | Can be used for example to bypass certain DNS layers like the Cloudflare proxy,
    | or resolve a domain to localhost.
    |
    */

    'resolve' => [
        'example.com' => '127.0.0.1',
    ],

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    |
    | Here you can specify the database connection that will be
    | used to save the SEO scores. When you set the save option to true, the
    | SEO score will be saved to the database.
    |
    */
    'database' => [
        'connection' => 'mysql',
        'save' => true,
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

    /*
    |--------------------------------------------------------------------------
    | Http client options
    |--------------------------------------------------------------------------
    |
    | Here you can specify the options of the http client. For example, in a
    | local development environment you may want to disable the SSL
    | certificate integrity check.
    |
    | An example of a http option:
    | 'verify' => false
    |
    */
    'http_options' => [],
];
