<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class SeLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'se';
    protected $englishName = '';
    protected $nativeName = 'Davvisámegiella';
    protected $isRtl = false;
}
