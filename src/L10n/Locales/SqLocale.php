<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class SqLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'sq';
    protected $englishName = 'Albanian';
    protected $nativeName = 'Shqip';
    protected $isRtl = false;
}
