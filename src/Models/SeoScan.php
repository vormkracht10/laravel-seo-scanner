<?php

namespace Backstage\Seo\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $url
 * @property string|null $model_type
 * @property int|string|null $model_id
 * @property int $pages
 * @property int $total_checks
 * @property array $failed_checks
 * @property float $time
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $started_at
 * @property Carbon $finished_at
 */
class SeoScan extends Model
{
    use Prunable;

    protected $guarded = [];

    protected $casts = [
        'failed_checks' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
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

    /**
     * The application model this scan was run for (e.g. a Domain or Site).
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subDays(config('seo.database.prune.older_than_days')));
    }
}
