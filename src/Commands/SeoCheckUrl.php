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
        $this->line($this->argument('url').' | <fg=green>'.$score->getSuccessfulChecks()->count().' passed</> <fg=red>'.($score->getFailedChecks()->count().' failed</>'));
        $this->line('');

        $score->getFailedChecks()->map(function ($failed) {
            $this->line('<fg=red>'.$failed->title.' failed.</>');

            if (property_exists($failed, 'failureReason')) {
                $this->line($failed->failureReason.' Estimated time to fix: '.$failed->timeToFix.' minute(s).');
            }

            $this->line('');
        });

        $totalChecks = $score->getFailedChecks()->count() + $score->getSuccessfulChecks()->count();

        $this->info('Completed '.$totalChecks.' out of '.getCheckCount().' checks.');

        return self::SUCCESS;
    }
}
