<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class El extends Locale
{
    use Rule1;

    protected $isoCode = 'el';
    protected $englishName = 'Greek';
    protected $nativeName = 'Ελληνικά';
    protected $isRtl = false;
}
