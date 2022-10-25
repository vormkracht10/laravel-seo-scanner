<?php

namespace Vormkracht10\Seo\Commands;

use Illuminate\Console\Command;

class SeoCheck extends Command
{
    public $signature = 'seo:check';

    public $description = 'Check the SEO score of your website';

    public int $success = 0;

    public int $failed = 0;

    public int $modelCount = 0;

    public function handle(): int
    {
        $model = config('seo.pages.model');

        $model = new $model();
        
        $model::all()->map(function ($test) {
            
            $seo = $test->seoScore();
            
            $this->failed += count($seo->getFailed());
            $this->success += count($seo->getSuccess());

            $score = $seo->getScore();

            $test->update(['seo_score' => $score]);

            $this->info($test->url . ' - ' . $score . ' SEO score');

            $this->modelCount++;
        });

        $this->info('Command completed with ' . $this->failed . ' failed and ' . $this->success . ' successful checks on ' . $this->modelCount . ' pages.');

        return self::SUCCESS;
    }
}
