<?php 

namespace Vormkracht10\Seo\Checks;

/**
 * @property string $title
 * @property string $priority
 * @property int $timeToFix
 * @property bool $success
 * @method handle()
 */
interface CheckInterface
{
    public function handle(string $url, string $response): self;
}