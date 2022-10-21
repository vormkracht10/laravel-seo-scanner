<?php

namespace Vormkracht10\Seo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Vormkracht10\Seo\Seo
 */
class Seo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Vormkracht10\Seo\Seo::class;
    }
}
