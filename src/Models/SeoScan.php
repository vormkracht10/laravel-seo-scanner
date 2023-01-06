<?php

namespace Vormkracht10\Seo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeoScan extends Model
{
    use Prunable;

    protected $guarded = [];

    protected $casts = [
        'failed_checks' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        if (! isset($this->connection)) {
            $this->setConnection(config('seo.database.connection'));
        }

        $this->setTable('seo_scans');

        parent::__construct($attributes);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(SeoScore::class);
    }
}
