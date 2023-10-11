<?php

namespace Vormkracht10\Seo\Commands;

use Illuminate\Console\Command;
use Vormkracht10\Seo\Facades\Seo;

class SeoScanUrl extends Command
{
    public $signature = 'seo:scan-url {url} {--javascript}';

    public $description = 'Scan the SEO score of a url';

    public function handle(): int
    {
        $this->info(__('Please wait while we scan your web page...'));
        $this->line('');

        $progress = $this->output->createProgressBar(getCheckCount());
        $progress->start();

        $score = Seo::check($this->argument('url'), $progress, $this->option('javascript'));

        $progress->finish();

        $this->line('');
        $this->line('');
        $this->line('-----------------------------------------------------------------------------------------------------------------------------------');
        $this->line('> '.$this->argument('url').' | <fg=green>'.$score->getSuccessfulChecks()->count().' '.__('passed').'</> <fg=red>'.($score->getFailedChecks()->count().' '.__('failed').'</>'));
        $this->line('-----------------------------------------------------------------------------------------------------------------------------------');
        $this->line('');

        // If successful and failed checks are empty, we can assume that the
        // visit page threw an exception. In that case, we don't want to
        // show the checks. But show the exception message instead.
        if ($score->getSuccessfulChecks()->isEmpty() && $score->getFailedChecks()->isEmpty()) {
            $this->line('<fg=red>✘ '.__('Unfortunately, the url that is used is not correct. Please try again with a different url').'</>');

            return self::FAILURE;
        }

        $score->getAllChecks()->each(function ($checks, $type) {
            $checks->each(function ($check) use ($type) {
                if ($type == 'failed') {
                    $this->line('<fg=red>✘ '.$check->title.' '.__('failed').'.</>');

                    if (property_exists($check, 'failureReason')) {
                        $this->line($check->failureReason.' '.__('Estimated time to fix: :time minute(s)', [
                            'time' => $check->timeToFix,
                        ]));

                        $this->line('');
                    }
                } else {
                    $this->line('<fg=green>✔ '.$check->title.'</>');
                }
            });

            $this->line('');
        });

        $totalChecks = $score->getFailedChecks()->count() + $score->getSuccessfulChecks()->count();

        $this->info(__('Completed :checks out of :total checks', [
            'checks' => $totalChecks,
            'total' => getCheckCount(),
        ]));
        $this->line('');

        cache()->driver(config('seo.cache.driver'))->tags('seo')->flush();

        return self::SUCCESS;
    }
}
