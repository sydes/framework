<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class FiLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'fi';
    protected $englishName = 'Finnish';
    protected $nativeName = 'Suomen kieli';
    protected $isRtl = false;
}
