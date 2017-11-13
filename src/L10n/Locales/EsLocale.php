<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class EsLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'es';
    protected $englishName = 'Spanish';
    protected $nativeName = 'español';
    protected $isRtl = false;
}
