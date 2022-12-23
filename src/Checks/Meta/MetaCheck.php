<?php

namespace Vormkracht10\Seo\Checks\Meta;

use Illuminate\Http\Client\Response;
use Vormkracht10\Seo\Checks\Check;

/**
 * @method getMetaContent()
 */
interface MetaCheck extends Check
{
    public function getMetaContent(Response $response): string|null;
}
