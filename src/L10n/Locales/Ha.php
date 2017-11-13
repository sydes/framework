<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class Ha extends Locale
{
    use Rule1;

    protected $isoCode = 'ha';
    protected $englishName = 'Hausa';
    protected $nativeName = '‫هَوُسَ‬';
    protected $isRtl = true;
}
