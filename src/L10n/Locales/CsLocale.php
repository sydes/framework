<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule8;

class CsLocale extends Locale
{
    use Rule8;

    protected $isoCode = 'cs';
    protected $englishName = 'Czech';
    protected $nativeName = 'čeština';
    protected $isRtl = false;
}
