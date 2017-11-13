<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class Sw extends Locale
{
    use Rule1;

    protected $isoCode = 'sw';
    protected $englishName = 'Swahili';
    protected $nativeName = 'Kiswahili';
    protected $isRtl = false;
}
