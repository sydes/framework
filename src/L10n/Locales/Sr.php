<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule7;

class Sr extends Locale
{
    use Rule7;

    protected $isoCode = 'sr';
    protected $englishName = 'Serbian';
    protected $nativeName = 'српски језик';
    protected $isRtl = false;
}
