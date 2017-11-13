<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule2;

class BrLocale extends Locale
{
    use Rule2;

    protected $isoCode = 'br';
    protected $englishName = 'Breton';
    protected $nativeName = 'brezhoneg';
    protected $isRtl = false;
}
