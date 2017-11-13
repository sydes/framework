<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class CaLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'ca';
    protected $englishName = 'Catalan';
    protected $nativeName = 'Català';
    protected $isRtl = false;
}
