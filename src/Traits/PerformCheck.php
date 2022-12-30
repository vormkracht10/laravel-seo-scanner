<?php

namespace Vormkracht10\Seo\Traits;

use Closure;

trait PerformCheck
{
    public function __invoke(array $data, Closure $next)
    {
        if (! in_array('exit', $data)) {
            $result = $this->check($data['response']);
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

            return $data;
        }

        $value = ['result' => $result];

        if (!$result) {
            $value['reason'] = $this->failureReason;
            $value['expectedValue'] = $this->expectedValue;
            $value['actualValue'] = $this->actualValue;
        }                

        $data['checks'][__CLASS__] = $value;

        return $data;
    }
}
