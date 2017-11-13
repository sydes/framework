<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule2;

class Fr extends Locale
{
    use Rule2;

    protected $isoCode = 'fr';
    protected $englishName = 'French';
    protected $nativeName = 'français';
    protected $isRtl = false;
}
