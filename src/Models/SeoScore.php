<?php

namespace Vormkracht10\Seo\Models;

use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoScore extends Model
{
    use Prunable,
        MassPrunable;

    protected $guarded = [];

    protected $casts = [
        'checks' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        if (! isset($this->connection)) {
            $this->setConnection(config('seo.database.connection'));
        }

        $this->setTable(config('seo.database.table_name'));

        parent::__construct($attributes);
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
