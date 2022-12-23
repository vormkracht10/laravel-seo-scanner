<?php

namespace Vormkracht10\Seo\Checks\Content;

use Vormkracht10\Seo\Checks\Check;
use Illuminate\Http\Client\Response;

/**
 * @method getContent()
 * @method validateContent()
 */
interface ContentCheck extends Check
{
    public function getContent(Response $response): string|array|null;

    public function validateContent(string|array $content): bool;
}