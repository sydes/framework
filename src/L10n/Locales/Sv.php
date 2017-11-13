<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class Sv extends Locale
{
    use Rule1;

    protected $isoCode = 'sv';
    protected $englishName = 'Swedish';
    protected $nativeName = 'Svenska';
    protected $isRtl = false;
}
