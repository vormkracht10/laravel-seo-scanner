<?php

namespace Vormkracht10\Seo\Checks\Traits;

trait FormatRequest
{
    public function formatRequest($request): array
    {
        // If the first check (ResponseCheck) fails, all other checks should fail.
        if (! $request['checks'][0]->checkSuccessful) {
            $this->checkSuccessful = false;
        }

        $previousChecks = $request['checks'];
        $previousChecks[] = $this;

        $request['checks'] = $previousChecks;

        return $request;
    }
}
