<?php

namespace Vormkracht10\Seo\Commands;

use Illuminate\Console\Command;

class SeoCommand extends Command
{
    public $signature = 'seo:check';

    public $description = 'Check the SEO score of your website';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
