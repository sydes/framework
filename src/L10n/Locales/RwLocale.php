<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class RwLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'rw';
    protected $englishName = 'Kinyarwanda';
    protected $nativeName = 'Kinyarwanda';
    protected $isRtl = false;
}
