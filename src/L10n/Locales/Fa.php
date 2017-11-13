<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class Fa extends Locale
{
    use Rule1;

    protected $isoCode = 'fa';
    protected $englishName = 'Persian';
    protected $nativeName = '‫فارسی‬';
    protected $isRtl = true;
}
