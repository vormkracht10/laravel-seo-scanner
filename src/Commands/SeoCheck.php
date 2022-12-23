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

        $model::all()->filter->url->map(function ($model) {
            $seo = $model->seoScore();

            $this->failed += count($seo->getFailed());
            $this->success += count($seo->getSuccessful());
            $this->modelCount++;

            $score = $seo->getScore();

            $model->update(['seo_score' => $score]);

            if ($score < 100) {
                $this->warn($model->url.' - '.$score.' SEO score');

                foreach($seo->getFailed() as $failed) {
                    $this->error($failed->title.' failed. Estimated time to fix: '.$failed->timeToFix.' minute(s).');
                }

                return;
            }
            
            $this->info($model->url.' - '.$score.' SEO score');
        });

        $this->info('Command completed with '.$this->failed.' failed and '.$this->success.' successful checks on '.$this->modelCount.' pages.');

        return self::SUCCESS;
    }
}
