<?php

namespace Vormkracht10\Seo\Traits;

trait Translatable
{
    public function getTranslatedDescription(): string
    {
        return __($this->description);
    }
}
