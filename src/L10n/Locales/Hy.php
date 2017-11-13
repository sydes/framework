<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class Hy extends Locale
{
    use Rule1;

    protected $isoCode = 'hy';
    protected $englishName = 'Armenian';
    protected $nativeName = 'Հայերեն';
    protected $isRtl = false;
}
