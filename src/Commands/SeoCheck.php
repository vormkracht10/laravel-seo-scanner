<?php

namespace Vormkracht10\Seo\Commands;

use Illuminate\Console\Command;

class SeoCheck extends Command
{
    public $signature = 'seo:check';

    public $description = 'Check the SEO score of your website';

    public array $success = [];

    public array $failed = [];

    public int $modelCount = 0;

    public function handle(): int
    {
        $model = config('seo.pages.model');

        $model = new $model();
        
        $model::all()->map(function ($model) {
            $seo = $model->seoScore();

            $this->failed = [$seo->getFailed(), ...$this->failed];
            $this->success = [$seo->getSuccess(), ...$this->success];

            $score = $seo->getScore();

            $model->update(['seo_score' => $score]);

            $this->info($model->url . ' - ' . $score . ' SEO score');

            $this->modelCount++;
        });

        $this->info('Command completed with ' . count($this->failed) . ' failed and ' . count($this->success) . ' successful checks on ' . $this->modelCount . ' pages.');

        return self::SUCCESS;
    }
}
