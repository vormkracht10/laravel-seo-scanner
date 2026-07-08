<?php

namespace Backstage\Seo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Backstage\Seo\SeoScore check(string $url, ?\Symfony\Component\Console\Helper\ProgressBar $progress = null, bool $useJavascript = false)
 * @method static \Backstage\Seo\Models\SeoScan scanDomain(string $domain, ?\Illuminate\Database\Eloquent\Model $subject = null, bool $queue = false, ?int $maxPages = null, ?bool $useJavascript = null)
 *
 * @see \Backstage\Seo\Seo
 */
class Seo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Backstage\Seo\Seo::class;
    }
}
