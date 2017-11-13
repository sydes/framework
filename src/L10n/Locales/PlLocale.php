<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule9;

class PlLocale extends Locale
{
    use Rule9;

    protected $isoCode = 'pl';
    protected $englishName = 'Polish';
    protected $nativeName = 'Polski';
    protected $isRtl = false;
}
