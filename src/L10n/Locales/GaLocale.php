<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule11;

class GaLocale extends Locale
{
    use Rule11;

    protected $isoCode = 'ga';
    protected $englishName = 'Irish';
    protected $nativeName = 'Gaeilge';
    protected $isRtl = false;
}
