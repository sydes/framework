<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class Fy extends Locale
{
    use Rule1;

    protected $isoCode = 'fy';
    protected $englishName = '';
    protected $nativeName = 'Frysk';
    protected $isRtl = false;
}
