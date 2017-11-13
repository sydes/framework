<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule2;

class TiLocale extends Locale
{
    use Rule2;

    protected $isoCode = 'ti';
    protected $englishName = 'Tigrinya';
    protected $nativeName = 'ትግርኛ';
    protected $isRtl = false;
}
