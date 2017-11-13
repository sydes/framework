<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class BgLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'bg';
    protected $englishName = 'Bulgarian';
    protected $nativeName = 'български език';
    protected $isRtl = false;
}
