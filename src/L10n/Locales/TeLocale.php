<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class TeLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'te';
    protected $englishName = 'Telugu';
    protected $nativeName = 'తెలుగు';
    protected $isRtl = false;
}
