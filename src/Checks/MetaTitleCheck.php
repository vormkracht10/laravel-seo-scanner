<?php 

namespace Vormkracht10\Seo\Checks;

class MetaTitleCheck implements CheckInterface
{
    public string $title = "Check if the title on the homepage does not contain 'home'";

    public string $priority = 'high';

    public int $timeToFix = 5;

    public function handle(): void
    {
        // 
    }
}