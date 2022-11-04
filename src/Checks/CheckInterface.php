<?php

namespace Vormkracht10\Seo\Checks;

use Closure;

/**
 * @property string $title
 * @property string $priority
 * @property int $timeToFix
 * @propertu int $scoreWeight
 * @property bool $checkSuccessful
 *
 * @method handle()
 */
interface CheckInterface
{
    public function handle($request, Closure $next): array;   
}
