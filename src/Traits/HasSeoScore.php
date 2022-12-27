<?php 

namespace Vormkracht10\Seo\Traits;

use Vormkracht10\Seo\Facades\Seo;
use Vormkracht10\Seo\Models\SeoScore;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasSeoScore
{
    public function seoScore(): SeoScore
    {
        return Seo::check(url: $this->url);
    }

    public function seoScores(): MorphMany
    {
        return $this->morphMany(SeoScore::class, 'model');
    }

    public function scopeWithSeoScores(Builder $query): Builder
    {
        return $query->with('seoScores');
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