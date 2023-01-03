<?php

namespace Vormkracht10\Seo\Traits;

use Closure;

trait PerformCheck
{
    public function __invoke(array $data, Closure $next)
    {
        if (! in_array('exit', $data)) {
            $result = $this->check($data['response'], $data['crawler']);
        }

        $result = $result ?? false;

        $data = $this->setResult($data, $result);

        if (! $result && ! $this->continueAfterFailure) {
            $data['exit'] = true;
        }

        return $next($data);
    }

    public function setResult(array $data, bool $result): array
    {
        if (in_array('exit', $data)) {
            unset($data['checks'][__CLASS__]);
        } else {
            $data['checks'][__CLASS__] = $result;
        }

        return $data;
    }
}
