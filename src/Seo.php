<?php

namespace Vormkracht10\Seo;

use Illuminate\Http\Client\Response;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Finder\Finder;

class Seo
{
    /**
     * @var ProgressBar|null The progress bar to use for the checks.
     */
    public ProgressBar|null $progress;

    public function __construct(
        protected Http $http,
        protected Collection $successful,
        protected Collection $failed,
    ) {
    }

    public function check(string $url, ProgressBar|null $progress = null): SeoScore
    {
        $response = $this->visitPage(url: $url);

        $this->progress = $progress;

        $this->runChecks(response: $response);

        return (new SeoScore)($this->successful, $this->failed);
    }

    private function visitPage(string $url): object
    {
        $response = $this->http::get(url: $url);

        return $response;
    }

    private function runChecks(Response $response): void
    {
        $checks = self::orderedCheckClasses();

        app(Pipeline::class)
            ->send([
                'response' => $response,
                'checks' => $checks,
                'progress' => $this->progress,
            ])
            ->through($checks->keys()->toArray())
            ->then(function ($data) {
                $this->successful = $data['checks']->filter(fn ($result) => $result['result'])
                    ->map(function ($result, $check) {
                        return app($check)->merge($result);
                    });

                $this->failed = $data['checks']->filter(fn ($result) => ! $result['result'])
                    ->map(function ($result, $check) {
                        return app($check)->merge($result);
                    });
            });
    }

    private static function getCheckPaths(): array
    {
        if (app()->runningUnitTests()) {
            return collect(config('seo.check_paths', [__DIR__.'/Checks']))
                ->toArray();
        }

        return collect(config('seo.check_paths', ['Vormkracht10\\Seo\\Checks' => __DIR__.'/Checks']))
            ->filter(fn ($dir) => file_exists($dir))
            ->toArray();
    }

    private static function getCheckClasses(): Collection
    {
        if (! in_array('*', Arr::wrap(config('seo.checks', '*')))) {
            return collect(Arr::wrap(config('seo.checks')))->mapWithKeys(fn ($check) => [$check => null]);
        }

        $checks = collect();

        if (empty($paths = self::getCheckPaths())) {
            return $checks;
        }

        collect($paths)->each(function ($path, $baseNamespace) use (&$checks) {
            if (app()->runningUnitTests()) {
                $path = __DIR__.'/Checks';
            }

            $files = is_dir($path) ? (new Finder)->in($path)->files() : Arr::wrap($path);

            foreach ($files as $fileInfo) {
                $checkClass = $baseNamespace.str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after(
                        is_string($fileInfo) ? $fileInfo : $fileInfo->getRealPath(),
                        realpath($path)
                    )
                );

                $checks->put($checkClass, null);
            }
        });

        if (empty($exclusions = config('seo.exclude_checks', []))) {
            return $checks;
        }

        return $checks->filter(fn ($check, $key) => ! in_array($key, $exclusions));
    }

    /**
     * Order the checks so that the checks where 'continueAfterFailure' is set to false comes first.
     * This way we can stop the pipeline when a check fails and we don't want to continue.
     *
     * @return Collection
     */
    private static function orderedCheckClasses(): Collection
    {
        return self::getCheckClasses()->map(fn ($check, $key) => app($key))
            ->sortBy(fn ($check) => $check->continueAfterFailure)
            ->mapWithKeys(fn ($check) => [$check::class => null]);
    }
}
