<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class MlLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'ml';
    protected $englishName = 'Malayalam';
    protected $nativeName = 'മലയാളം';
    protected $isRtl = false;
}
