<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule0;

class DzLocale extends Locale
{
    use Rule0;

    protected $isoCode = 'dz';
    protected $englishName = 'Dzongkha';
    protected $nativeName = 'རྫོང་ཁ';
    protected $isRtl = false;
}
