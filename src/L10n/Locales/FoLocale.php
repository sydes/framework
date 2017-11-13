<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class FoLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'fo';
    protected $englishName = 'Faroese';
    protected $nativeName = 'Føroyskt';
    protected $isRtl = false;
}
