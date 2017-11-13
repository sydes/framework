<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class Pa extends Locale
{
    use Rule1;

    protected $isoCode = 'pa';
    protected $englishName = 'Panjabi';
    protected $nativeName = 'ਪੰਜਾਬੀ‫پنجابی‬';
    protected $isRtl = true;
}
