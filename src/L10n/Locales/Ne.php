<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class Ne extends Locale
{
    use Rule1;

    protected $isoCode = 'ne';
    protected $englishName = 'Nepali';
    protected $nativeName = 'नेपाली';
    protected $isRtl = false;
}
