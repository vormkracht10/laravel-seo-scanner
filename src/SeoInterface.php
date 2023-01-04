<?php

namespace Vormkracht10\Seo;

use Vormkracht10\Seo\Models\SeoScore;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface SeoInterface
{
    public function getUrlAttribute(): string|null;
}
