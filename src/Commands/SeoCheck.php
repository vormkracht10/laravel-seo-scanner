<?php

namespace Vormkracht10\Seo\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Vormkracht10\Seo\SeoScore;

class SeoCheck extends Command
{
    public $signature = 'seo:check';

    public $description = 'Check the SEO score of your website';

    public int $success = 0;

    public int $failed = 0;

    public int $modelCount = 0;

    public function handle(): int
    {
        if (empty(config('seo.models'))) {
            $this->error('No models specified in config/seo.php');

            return self::FAILURE;
        }

        foreach (config('seo.models') as $model) {
            $this->calculateScoreForModels($model);
        }

        $this->info('Command completed with '.$this->failed.' failed and '.$this->success.' successful checks on '.$this->modelCount.' pages.');

        return self::SUCCESS;
    }

    private function calculateScoreForModels(string $model)
    {
        $model = new $model();

        $model::all()->filter->url->map(function ($model) {
            $seo = $model->seoScore();

            $this->failed += count($seo->getFailedChecks());
            $this->success += count($seo->getSuccessfulChecks());
            $this->modelCount++;

            if (config('seo.database.save')) {
                $this->saveScoreToDatabase($seo, $model);
            }
    
            $this->logResultToConsole($seo, $model);
        });
    }

    private function saveScoreToDatabase(SeoScore $seo, object $model): void
    {
        $score = $seo->getScore();

        DB::table(config('seo.database.table_name'))
            ->insert([
                'url' => $model->url,
                'model_type' => $model->getMorphClass(),
                'model_id' => $model->id,
                'score' => $score,
                'checks' => json_encode([
                    'failed' => $seo->getFailedChecks(),
                    'successful' => $seo->getSuccessfulChecks(),
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    }

    private function logResultToConsole(SeoScore $seo, object $model): void
    {
        $score = $seo->getScore();

        if ($score < 100) {
            $this->warn($model->url.' - '.$score.' SEO score');

            $seo->getFailedChecks()->map(function ($failed) {
                $this->error($failed->title.' failed. Estimated time to fix: '.$failed->timeToFix.' minute(s).');
            });

            return;
        }

        $this->info($model->url.' - '.$score.'%');
    }
}
