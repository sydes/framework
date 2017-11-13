<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule0;

class KmLocale extends Locale
{
    use Rule0;

    protected $isoCode = 'km';
    protected $englishName = 'Khmer';
    protected $nativeName = 'ភាសាខ្មែរ';
    protected $isRtl = false;
}
