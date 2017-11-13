<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class EoLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'eo';
    protected $englishName = 'Esperanto';
    protected $nativeName = 'Esperanto';
    protected $isRtl = false;
}
