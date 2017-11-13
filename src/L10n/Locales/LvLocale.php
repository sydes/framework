<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule3;

class LvLocale extends Locale
{
    use Rule3;

    protected $isoCode = 'lv';
    protected $englishName = 'Latvian';
    protected $nativeName = 'latviešu valoda';
    protected $isRtl = false;
}
