<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule7;

class BeLocale extends Locale
{
    use Rule7;

    protected $isoCode = 'be';
    protected $englishName = 'Belarusian';
    protected $nativeName = 'Беларуская';
    protected $isRtl = false;
}
