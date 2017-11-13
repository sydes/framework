<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class EtLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'et';
    protected $englishName = 'Estonian';
    protected $nativeName = 'Eesti keel';
    protected $isRtl = false;
}
