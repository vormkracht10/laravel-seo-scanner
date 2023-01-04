<?php

namespace Vormkracht10\Seo;

use Vormkracht10\Seo\Models\SeoScore;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface SeoInterface
{
    public function seoScore(): SeoScore;

    public function seoScores(): MorphMany;

    public function scopeWithSeoScores(Builder $query): Builder;

    public function getCurrentScore(): int;

    public function getUrlAttribute(): string|null;

    public function getCurrentScoreDetails(): array;
}
