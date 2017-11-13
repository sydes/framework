<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule10;

class SlLocale extends Locale
{
    use Rule10;

    protected $isoCode = 'sl';
    protected $englishName = 'Slovenian';
    protected $nativeName = 'slovenščina';
    protected $isRtl = false;
}
