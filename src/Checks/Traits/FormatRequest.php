<?php

namespace Vormkracht10\Seo\Checks\Traits;

trait FormatRequest
{
    public function formatRequest($request): array
    {
        $previousChecks = $request['checks'];
        $previousChecks[] = $this;

        $request['checks'] = $previousChecks;

        return $request;
    }
}
