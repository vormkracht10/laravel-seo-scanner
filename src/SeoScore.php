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
        if (! $successful->count()) {
            return 0;
        }

        $successfulScoreWeight = $successful->sum('scoreWeight');
        $failedScoreWeight = $failed->sum('scoreWeight');
        $totalScoreWeight = $successfulScoreWeight + $failedScoreWeight;

<<<<<<< HEAD
        $this->score = round($successfulScoreWeight / $totalScoreWeight * 100);
        $this->successful = $successful;
        $this->failed = $failed;

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
=======
        return round(($totalScoreWeight / $successfulScoreWeight) * 100);
>>>>>>> f04906937674ee083b92d6cd56dadff9f200d4b1
    }
}
