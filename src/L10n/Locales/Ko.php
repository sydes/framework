<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule0;

class Ko extends Locale
{
    use Rule0;

    protected $isoCode = 'ko';
    protected $englishName = 'Korean';
    protected $nativeName = '한국어';
    protected $isRtl = false;
}
