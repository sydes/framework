<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class KlLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'kl';
    protected $englishName = 'Kalaallisut';
    protected $nativeName = 'kalaallisut';
    protected $isRtl = false;
}
