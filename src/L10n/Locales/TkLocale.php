<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class TkLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'tk';
    protected $englishName = 'Turkmen';
    protected $nativeName = 'Türkmen';
    protected $isRtl = false;
}
