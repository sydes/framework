<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class Ff extends Locale
{
    use Rule1;

    protected $isoCode = 'ff';
    protected $englishName = 'Fulah';
    protected $nativeName = 'Fulfulde';
    protected $isRtl = false;
}
