<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule13;

class MtLocale extends Locale
{
    use Rule13;

    protected $isoCode = 'mt';
    protected $englishName = 'Maltese';
    protected $nativeName = 'Malti';
    protected $isRtl = false;
}
