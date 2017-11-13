<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule2;

class Wa extends Locale
{
    use Rule2;

    protected $isoCode = 'wa';
    protected $englishName = 'Walloon';
    protected $nativeName = 'Walon';
    protected $isRtl = false;
}
