<?php

namespace Vormkracht10\Seo\Commands;

use Illuminate\Console\Command;
use Vormkracht10\Seo\Facades\Seo;

class SeoCheckUrl extends Command
{
    public $signature = 'seo:check-url {url}';

    public $description = 'Check the SEO score of a url';

    public function handle(): int
    {
        $this->info('Please wait while we check your web page...');
        $this->line('');

        $progress = $this->output->createProgressBar(18);
        $progress->start();

        $score = Seo::check($this->argument('url'), $progress);

        $progress->finish();

        $this->line('');
        $this->line('');
        $this->line('-----------------------------------------------------------------------------------------------------------------------------------');
        $this->line('> '.$this->argument('url').' | <fg=green>'.$score->getSuccessfulChecks()->count().' passed</> <fg=red>'.($score->getFailedChecks()->count().' failed</>'));
        $this->line('-----------------------------------------------------------------------------------------------------------------------------------');
        $this->line('');

        if ($score < 100) {
            // If successful and failed checks are empty, we can assume that the
            // visit page threw an exception. In that case, we don't want to
            // show the checks. But show the exception message instead.
            if ($score->getSuccessfulChecks()->isEmpty() && $score->getFailedChecks()->isEmpty()) {
                $this->line('<fg=red>✘ Unfortunately, the url you entered is not correct. Please try again with a different url.</>');

                return self::FAILURE;
            }

            $score->getAllChecks()->each(function ($checks, $type) {
                $checks->each(function ($check) use ($type) {
                    if ($type == 'failed') {
                        $this->line('<fg=red>✘ '.$check->title.' failed.</>');

                        if (property_exists($check, 'failureReason')) {
                            $this->line($check->failureReason.' Estimated time to fix: '.$check->timeToFix.' minute(s).');

                            $this->line('');
                        }
                    } else {
                        $this->line('<fg=green>✔ '.$check->title.'</>');
                    }
                });

                $this->line('');
            });

            $totalChecks = $score->getFailedChecks()->count() + $score->getSuccessfulChecks()->count();
        }
        
        $this->info('Completed '.$totalChecks.' out of '.getCheckCount().' checks.');

        return self::SUCCESS;
    }
}
