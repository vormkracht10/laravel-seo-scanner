<?php

namespace Backstage\Seo\Commands;

use Backstage\Seo\Services\DomainScanner;
use Illuminate\Console\Command;

class SeoScanDomain extends Command
{
    public $signature = 'seo:scan-domain
        {domain : The domain to scan, e.g. example.com}
        {--queue : Dispatch the page scans as batched queue jobs instead of running them synchronously}
        {--max-pages= : Maximum number of pages to scan, defaults to seo.crawl.max_pages}
        {--javascript : Render pages with javascript before running the checks}
        {--format=console : The output format (console or json)}';

    public $description = 'Scan the SEO score of an external domain';

    public function handle(DomainScanner $scanner): int
    {
        $json = $this->option('format') === 'json';

        if (! $json) {
            $this->info('Please wait while we scan '.$this->argument('domain').'...');
            $this->line('');
        }

        try {
            $scan = $scanner->scan(
                domain: $this->argument('domain'),
                queue: $this->option('queue'),
                maxPages: $this->option('max-pages') ? (int) $this->option('max-pages') : null,
                useJavascript: $this->option('javascript') ?: null,
            );
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        if ($this->option('queue')) {
            $this->info('Dispatched scan #'.$scan->id.' for '.$scan->url.' to the queue.');

            return self::SUCCESS;
        }

        if ($json) {
            $this->output->writeln(json_encode([
                'scan_id' => $scan->id,
                'url' => $scan->url,
                'pages' => $scan->pages,
                'failed_checks' => $scan->failed_checks,
                'average_score' => (int) round($scan->scores()->avg('score') ?? 0),
                'time' => $scan->time,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            return self::SUCCESS;
        }

        $this->info('Scanned '.$scan->pages.' page(s) on '.$scan->url.' in '.round((float) $scan->time, 2).'s.');
        $this->line('Average score: '.(int) round($scan->scores()->avg('score') ?? 0).'/100');

        if (! empty($scan->failed_checks)) {
            $this->line('');
            $this->line('<fg=red>Failed checks across the scanned pages:</>');

            foreach ($scan->failed_checks as $check) {
                $this->line('<fg=red>✘ '.class_basename($check).'</>');
            }
        }

        return self::SUCCESS;
    }
}
