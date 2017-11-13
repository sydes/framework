<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class En extends Locale
{
    use Rule1;

    protected $isoCode = 'en';
    protected $englishName = 'English';
    protected $nativeName = 'English';
    protected $isRtl = false;
}
