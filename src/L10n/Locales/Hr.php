<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule7;

class Hr extends Locale
{
    use Rule7;

    protected $isoCode = 'hr';
    protected $englishName = 'Croatian';
    protected $nativeName = 'Hrvatski';
    protected $isRtl = false;
}
