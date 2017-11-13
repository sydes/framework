<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class BnLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'bn';
    protected $englishName = 'Bengali';
    protected $nativeName = 'বাংলা';
    protected $isRtl = false;
}
