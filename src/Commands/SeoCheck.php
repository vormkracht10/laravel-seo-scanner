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

        $this->info('Please wait while we check your web page(s)...');
        $this->line('');

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

        // Check if all routes should be checked
        if (in_array('*', Arr::wrap(config('seo.routes')))) {
            $routes = $routes->map(fn ($route) => $route->uri);
        } else {
            // Only check the routes specified in the config
            $routes = $routes->filter(fn ($route) => in_array($route->getName(), Arr::wrap(config('seo.routes'))))
                ->map(fn ($route) => $route->uri);
        }

        // Filter out excluded routes by name
        if (config('seo.exclude_routes')) {
            $routes = $routes->filter(fn ($route) => ! in_array($route, config('seo.exclude_routes')));
        }

        // Filter out excluded routes by path
        if (config('seo.exclude_paths')) {
            $routes = $routes->filter(function ($route) {
                foreach (config('seo.exclude_paths') as $path) {
                    // if path contains a wildcard, check if the route starts with the path
                    if (str_contains($path, '*')) {
                        $path = str_replace('/*', '', $path);

                        if (str_starts_with($route, $path)) {
                            return false;
                        }
                    }

                    // if path does not contain a wildcard, check if the route contains the path
                    if (str_contains($route, $path)) {
                        return false;
                    }
                }

                return true;
            });
        }

        // Exclude routes that contain a parameter or where it ends with .txt or .xml
        $routes = $routes->filter(fn ($route) => ! str_contains($route, '{') &&
            ! str_ends_with($route, '.txt') &&
            ! str_ends_with($route, '.xml')
        );

        return $routes;
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

        $this->line($url.' | <fg=green>'.$seo->getSuccessfulChecks()->count().' passed</> <fg=red>'.($seo->getFailedChecks()->count().' failed</>'));
        $this->line('');

        if ($score < 100) {
            $seo->getFailedChecks()->map(function ($failed) {
                $this->line('<fg=red>'.$failed->title.' failed.</>');

                if (property_exists($failed, 'failureReason')) {
                    $this->line($failed->failureReason.' Estimated time to fix: '.$failed->timeToFix.' minute(s).');
                }
                
                $this->line('');
            });

            return;
        }
    }
}
