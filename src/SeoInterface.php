<?php

namespace Vormkracht10\Seo;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Vormkracht10\Seo\Models\SeoScore;

interface SeoInterface
{
    public function seoScore(): SeoScore;

    public function seoScores(): MorphMany;

    public function scopeWithSeoScores(Builder $query): Builder;

    public function getCurrentScore(): int;

    public function getUrlAttribute(): string|null;

    public function getCurrentScoreDetails(): array;
}
