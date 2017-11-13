<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class OrLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'or';
    protected $englishName = 'Oriya';
    protected $nativeName = 'ଓଡ଼ିଆ';
    protected $isRtl = false;
}
