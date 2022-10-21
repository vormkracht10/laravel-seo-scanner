<?php 

namespace Vormkracht10\Seo\Checks\Traits;

trait ValidateResponse
{
    public function validateResponse(object $response): bool
    {
        if ($response->getStatusCode() == 200) {
            return true;
        }

        return false;
    }
}