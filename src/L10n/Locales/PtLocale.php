<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class PtLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'pt';
    protected $englishName = 'Portuguese';
    protected $nativeName = 'Português';
    protected $isRtl = false;
}
