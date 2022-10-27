<?php

namespace Vormkracht10\Seo;

use Illuminate\Support\Collection;

class SeoScore
{
    public function __invoke(Collection $successful, Collection $failed): int
    {
        if (! $successful->count()) {
            return 0;
        }

        $successfulScoreWeight = $successful->sum('scoreWeight');
        $failedScoreWeight = $failed->sum('scoreWeight');
        $totalScoreWeight = $successfulScoreWeight + $failedScoreWeight;
        
        return round(($totalScoreWeight / $successfulScoreWeight) * 100);
    }
}
