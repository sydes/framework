<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule8;

class SkLocale extends Locale
{
    use Rule8;

    protected $isoCode = 'sk';
    protected $englishName = 'Slovak';
    protected $nativeName = 'slovenčina';
    protected $isRtl = false;
}
