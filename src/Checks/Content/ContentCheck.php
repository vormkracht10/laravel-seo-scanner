<?php

namespace Vormkracht10\Seo\Checks\Content;

use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Checks\Check;

/**
 * @method getContent()
 * @method validateContent()
 */
interface ContentCheck extends Check
{
    public function getContent(Response $response): string|array|null;

    public function validateContent(string|array $content): bool;
}
