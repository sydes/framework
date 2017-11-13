<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule2;

class TrLocale extends Locale
{
    use Rule2;

    protected $isoCode = 'tr';
    protected $englishName = 'Turkish';
    protected $nativeName = 'Türkçe';
    protected $isRtl = false;
}
