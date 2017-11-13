<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class ItLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'it';
    protected $englishName = 'Italian';
    protected $nativeName = 'Italiano';
    protected $isRtl = false;
}
