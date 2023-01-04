<?php

namespace Vormkracht10\Seo\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Vormkracht10\Seo\Facades\Seo;
use Vormkracht10\Seo\Models\SeoScore as SeoScoreModel;
use Vormkracht10\Seo\SeoScore;

trait HasSeoScore
{
    public function seoScore(): SeoScore
    {
        return Seo::check(url: $this->url);
    }

    public function seoScores(): MorphMany
    {
        return $this->morphMany(SeoScoreModel::class, 'model');
    }

    public function scopeWithSeoScores(Builder $query): Builder
    {
        return $query->whereHas('seoScores');
    }

    public function getCurrentScore(): int
    {
        return $this->seoScore()->getScore();
    }

    public function getCurrentScoreDetails(): array
    {
        return $this->seoScore()->getScoreDetails();
    }
}
