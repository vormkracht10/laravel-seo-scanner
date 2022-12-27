<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    |
    | Here you can specify which pages you want to check. When you specify a
    | model, the SEO score will be saved to the database. This way you can
    | check the SEO score of a specific page.
    |
    */
    'database' => [
        'connection' => 'mysql',
        'table_name' => 'seo_scores',
        'model' => null,
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
];
