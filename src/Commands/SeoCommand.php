<?php

namespace Vormkracht10\Seo\Commands;

use Illuminate\Console\Command;

class SeoCommand extends Command
{
    public $signature = 'laravel-seo';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
