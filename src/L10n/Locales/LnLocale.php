<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule2;

class LnLocale extends Locale
{
    use Rule2;

    protected $isoCode = 'ln';
    protected $englishName = 'Lingala';
    protected $nativeName = 'Lingála';
    protected $isRtl = false;
}
