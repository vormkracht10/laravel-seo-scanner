<?php

namespace Vormkracht10\Seo;

use Vormkracht10\Seo\SeoScore;

interface SeoInterface
{
    public function seoScore(): SeoScore;

    public function getScore(): int;
}