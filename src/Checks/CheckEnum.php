<?php

namespace Vormkracht10\Seo\Checks;

enum CheckEnum: string
{
    case META_TITLE_CHECK = MetaTitleCheck::class;
    case META_TITLE_LENGTH_CHECK = MetaTitleLengthCheck::class;
    case RESPONSE_CHECK = ResponseCheck::class;
}
