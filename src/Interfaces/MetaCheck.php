<?php

namespace Vormkracht10\Seo\Interfaces;

use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Interfaces\Check;

/**
 * @method getContent()
 * @method validateContent()
 */
interface MetaCheck extends Check
{
    public function getContent(Response $response): string|null;

    public function validateContent(string $content): bool;
}
