<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule0;

class KyLocale extends Locale
{
    use Rule0;

    protected $isoCode = 'ky';
    protected $englishName = 'Kirghiz';
    protected $nativeName = 'кыргыз тили';
    protected $isRtl = false;
}
