<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class NnLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'nn';
    protected $englishName = '';
    protected $nativeName = 'Nynorsk';
    protected $isRtl = false;
}
