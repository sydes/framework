<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule2;

class UzLocale extends Locale
{
    use Rule2;

    protected $isoCode = 'uz';
    protected $englishName = 'Uzbek';
    protected $nativeName = 'O\'zbek';
    protected $isRtl = true;
}
