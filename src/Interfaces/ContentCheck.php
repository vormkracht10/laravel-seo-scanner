<?php

namespace Vormkracht10\Seo\Interfaces;

use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\Check;

/**
 * @method getContent()
 * @method validateContent()
 */
interface ContentCheck extends Check
{
    public function getContent(Response $response): string|array|null;

    public function validateContent(string|array $content): bool;
}
