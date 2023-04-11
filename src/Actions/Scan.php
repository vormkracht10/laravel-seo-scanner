<?php 

namespace Vormkracht10\Seo\Actions;

use Illuminate\Support\Facades\Artisan;
use Lorisleiva\Actions\Concerns\AsAction;

class Scan
{
    use AsAction; 

    public string $jobQueue = 'seo';
    public int $jobTimeout = 3600;
    public int $jobTries = 3;
    
    public function handle(): void
    {
        Artisan::call('seo:scan');
    }

    public function asJob(): void
    {
        Artisan::queue('seo:scan');
    }

}