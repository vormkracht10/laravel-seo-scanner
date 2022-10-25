<?php

namespace Vormkracht10\Seo;

interface SeoInterface
{
    public function seoScore(): SeoScore;

    public function getScore(): int;
}
