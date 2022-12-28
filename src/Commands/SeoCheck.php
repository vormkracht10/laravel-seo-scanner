<?php

namespace Vormkracht10\Seo\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Vormkracht10\Seo\Facades\Seo;
use Vormkracht10\Seo\SeoScore;

class SeoCheck extends Command
{
    public $signature = 'seo:check';

    public $description = 'Check the SEO score of your website';

    public int $success = 0;

    public int $failed = 0;

    public int $modelCount = 0;

    public int $routeCount = 0;

    public function handle(): int
    {
        if (empty(config('seo.models')) && ! config('seo.check_routes')) {
            $this->error('No models or routes specified in config/seo.php');

            return self::FAILURE;
        }

        if (config('seo.check_routes')) {
            $this->calculateScoreForRoutes();
        }

        if (config('seo.models')) {
            foreach (config('seo.models') as $model) {
                $this->calculateScoreForModel($model);
            }
        }

        $totalPages = $this->modelCount + $this->routeCount;

        $this->info('Command completed with '.$this->failed.' failed and '.$this->success.' successful checks on '.$totalPages.' pages.');

        return self::SUCCESS;
    }

    private function calculateScoreForRoutes(): void
    {
        $routes = self::getRoutes();

        $routes->each(function ($path, $name) {
            $seo = Seo::check(url: route($name));

            $this->failed += count($seo->getFailedChecks());
            $this->success += count($seo->getSuccessfulChecks());
            $this->routeCount++;

            if (config('seo.database.save')) {
                $this->saveScoreToDatabase(seo: $seo, url: route($name));
            }

            $this->logResultToConsole($seo, route($name));
        });
    }

    private static function getRoutes(): Collection
    {
        $routes = collect(app('router')->getRoutes()->getRoutesByName())
            ->filter(fn ($route) => $route->methods[0] === 'GET');

        if (! in_array('*', Arr::wrap(config('seo.routes')))) {
            return $routes->map(fn ($route) => $route->uri);
        }

        return $routes->filter(fn ($route, $key) => ! in_array($key, config('seo.exclude_routes')))
            ->map(fn ($route) => $route->uri);
    }

    private function calculateScoreForModel(string $model)
    {
        $model = new $model();

        $model::all()->filter->url->map(function ($model) {
            $seo = $model->seoScore();

            $this->failed += count($seo->getFailedChecks());
            $this->success += count($seo->getSuccessfulChecks());
            $this->modelCount++;

            if (config('seo.database.save')) {
                $this->saveScoreToDatabase(seo: $seo, url: $model->url, model: $model);
            }

            $this->logResultToConsole(seo: $seo, url: $model->url);
        });
    }

    private function saveScoreToDatabase(SeoScore $seo, string $url, object|null $model = null): void
    {
        $score = $seo->getScore();

        DB::table(config('seo.database.table_name'))
            ->insert([
                'url' => $url,
                'model_type' => $model ? $model->getMorphClass() : null,
                'model_id' => $model ? $model->id : null,
                'score' => $score,
                'checks' => json_encode([
                    'failed' => $seo->getFailedChecks(),
                    'successful' => $seo->getSuccessfulChecks(),
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    }

    private function logResultToConsole(SeoScore $seo, string $url): void
    {
        $score = $seo->getScore();

        if ($score < 100) {
            $this->warn($url.' - '.$score.' SEO score');

            $seo->getFailedChecks()->map(function ($failed) {
                $this->error($failed->title.' failed. Estimated time to fix: '.$failed->timeToFix.' minute(s).');
            });

            return;
        }

        $this->info($url.' - '.$score.'%');
    }
}
