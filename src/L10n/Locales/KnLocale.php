<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class KnLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'kn';
    protected $englishName = 'Kannada';
    protected $nativeName = 'ಕನ್ನಡ';
    protected $isRtl = false;
}
