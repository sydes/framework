<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule2;

class AmLocale extends Locale
{
    use Rule2;

    protected $isoCode = 'am';
    protected $englishName = 'Amharic';
    protected $nativeName = 'አማርኛ';
    protected $isRtl = false;
}
