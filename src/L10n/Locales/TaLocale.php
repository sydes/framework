<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class TaLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'ta';
    protected $englishName = 'Tamil';
    protected $nativeName = 'தமிழ்';
    protected $isRtl = false;
}
