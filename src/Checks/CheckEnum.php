<?php 

namespace Vormkracht10\Seo\Checks;

use Vormkracht10\Seo\Checks\Traits\EnumsToArray;

enum CheckEnum: string
{
    use EnumsToArray;

    case META_TITLE_CHECK = MetaTitleCheck::class;
    case META_TITLE_LENGTH_CHECK = MetaTitleLengthCheck::class;
    case RESPONSE_CHECK = ResponseCheck::class;
}

