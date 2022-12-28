<?php

namespace Vormkracht10\Seo\Traits;

use Closure;

trait PerformCheck
{
    public function __invoke(array $data, Closure $next)
    {
        $result = $this->check($data['response']);

        $data = $this->setResult($data, $result);

        return $next($data);
    }

    public function setResult(array $data, bool $result): array
    {
        $data['checks'][__CLASS__] = $result;
        
        return $data;
    }
}
