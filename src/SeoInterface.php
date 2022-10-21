
<?php

namespace Spatie\Feed;

use Vormkracht10\Seo\SeoScore;

interface Feedable
{
    public function seoScore(): SeoScore;
}