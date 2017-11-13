<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule2;

class AkLocale extends Locale
{
    use Rule2;

    protected $isoCode = 'ak';
    protected $englishName = 'Akan';
    protected $nativeName = 'Akan';
    protected $isRtl = false;
}
