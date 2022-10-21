<?php

namespace Vormkracht10\Seo;

class SeoScore
{
    public function __construct(
        public array $success = [],
        public array $failed = [],
        public int $score = 0,
    ) {
        $this->score = $this->calculateScore($success, $failed);
    }

    private function calculateScore(array $succes, array $failed): int
    {
        $total = count($succes) + count($failed);

        if ($total === 0) {
            return 0;
        }

        return (count($succes) / $total) * 100;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function getSuccess(): array
    {
        return $this->success;
    }

    public function getFailed(): array
    {
        return $this->failed;
    }
}
