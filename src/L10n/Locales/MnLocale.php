<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class MnLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'mn';
    protected $englishName = 'Mongolian';
    protected $nativeName = 'Монгол';
    protected $isRtl = false;
}
