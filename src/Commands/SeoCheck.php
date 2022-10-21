<?php

namespace Vormkracht10\Seo\Commands;

use Illuminate\Console\Command;

class SeoCheck extends Command
{
    public $signature = 'seo:check';

    public $description = 'Check the SEO score of your website';

    public function handle(): int
    {
        $model = config('seo.pages.model');

        $model = new $model();
        
        $model::all()->map(function ($model) {
            $score = $model->getScore();

            $model->update(['seo_score' => $score]);

            $this->info($model->url . ' - ' . $score . '%');
        });

        $this->info('All done!');

        return self::SUCCESS;
    }
}
