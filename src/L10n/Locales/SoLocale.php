<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class SoLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'so';
    protected $englishName = 'Somali';
    protected $nativeName = 'Soomaaliga';
    protected $isRtl = false;
}
