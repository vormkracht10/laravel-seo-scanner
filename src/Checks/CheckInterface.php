<?php

namespace Vormkracht10\Seo\Checks;

use Closure;

/**
 * @property string $title
 * @property string $priority
 * @property int $timeToFix
 * @property bool $success
 *
 * @method handle()
 */
interface CheckInterface
{
    public function handle($request, Closure $next): Closure;   
}
