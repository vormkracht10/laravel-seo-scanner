<?php

namespace Vormkracht10\Seo;

use Illuminate\Support\Collection;

class SeoScore
{
    public $score = 0;

    public $successful;

    public $failed;

    public function __invoke(Collection $successful, Collection $failed)
    {
        $this->successful = $successful;
        $this->failed = $failed;
        
        if (! $successful->count()) {
            $this->score = 0;

            return $this;
        }

        $successfulScoreWeight = $successful->sum('scoreWeight');
        $failedScoreWeight = $failed->sum('scoreWeight');
        $totalScoreWeight = $successfulScoreWeight + $failedScoreWeight;

        $this->score = round($successfulScoreWeight / $totalScoreWeight * 100);

        return $this;
    }

    public function getScore()
    {
        return $this->score;
    }

    public function getFailed()
    {
        return $this->failed;
    }

    public function getSuccessful()
    {
        return $this->successful;
    }
}
