<?php 

namespace Vormkracht10\Seo\Checks;

/**
 * @property string $title
 * @property string $priority
 * @property int $timeToFix
 * @method handle()
 */
interface CheckInterface
{
    public function handle(): void;
}