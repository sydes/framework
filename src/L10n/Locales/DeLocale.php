<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class DeLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'de';
    protected $englishName = 'German';
    protected $nativeName = 'Deutsch';
    protected $isRtl = false;
}
