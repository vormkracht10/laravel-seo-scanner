<?php

namespace Vormkracht10\Seo\Interfaces;

use Closure;
use Illuminate\Http\Client\Response;

/**
 * @property string $title
 * @property string $priority
 * @property int $timeToFix
 * @property int $scoreWeight
 * @property bool $continueAfterFailure
 *
 * @method check()
 * @method __invoke()
 * @method setResult()
 */
interface Check
{
    public function check(Response $response): bool;

    public function __invoke(array $data, Closure $next);

    public function setResult(array $data, bool $result): array;
}
