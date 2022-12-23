<?php

namespace Vormkracht10\Seo\Checks\Meta;

use Vormkracht10\Seo\Checks\Check;
use Illuminate\Http\Client\Response;

/**
 * @method getMetaContent()
 */
interface MetaCheck extends Check
{
    public function getMetaContent(Response $response): string|null;
}