<?php

namespace Vormkracht10\Seo;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class Seo
{
    public function __construct(
        protected Http $http,
        protected Collection $successful,
        protected Collection $failed,
    ) {
    }

    public function check(string $url): SeoScore
    {
        $response = $this->visitPage(url: $url);

        $this->runChecks(response: $response);

        return (new SeoScore)($this->successful, $this->failed);
    }

    private function visitPage(string $url): object
    {
        $response = $this->http::get(url: $url);

        return $response;
    }

    private function runChecks(object $response): void
    {
        $checks = app(Pipeline::class)
            ->send($response)
            ->through(self::getCheckClasses())
            ->thenReturn();

        $checks = collect($checks['checks']);

        $this->successful = $checks->filter(fn ($check) => $check->checkSuccessful);
        $this->failed = $checks->filter(fn ($check) => ! $check->checkSuccessful);
    }

    private static function getCheckPaths(): array
    {
        return collect(config('seo.check_paths', ['Vormkracht10\\Seo\\Checks' => __DIR__.'/Checks']))
            ->filter(fn ($dir) => file_exists($dir))
            ->toArray();
    }

    private static function getCheckClasses(): array
    {
        if (! in_array('*', Arr::wrap(config('seo.checks', '*')))) {
            return Arr::wrap(config('seo.checks'));
        }

        $checks = [];

        if (empty($paths = self::getCheckPaths())) {
            return $checks;
        }

        collect($paths)->each(function ($path, $baseNamespace) use (&$checks) {
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

                $checks[] = $checkClass;
            }
        });

        if (empty($exclusions = config('seo.exclude_checks', []))) {
            return $checks;
        }

        return collect($checks)->filter(function ($checkClass) use ($exclusions) {
            return ! in_array($checkClass, $exclusions);
        })->toArray();
    }
}
