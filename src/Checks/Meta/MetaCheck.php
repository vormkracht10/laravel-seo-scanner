<?php

namespace Vormkracht10\Seo\Checks\Meta;

use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Checks\Check;

/**
 * @method getContent()
 * @method validateContent()
 */
interface MetaCheck extends Check
{
    public function getContent(Response $response): string|null;

    public function validateContent(string $content): bool;
}
