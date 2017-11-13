<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule0;

class SuLocale extends Locale
{
    use Rule0;

    protected $isoCode = 'su';
    protected $englishName = 'Sundanese';
    protected $nativeName = 'Basa Sunda';
    protected $isRtl = false;
}
