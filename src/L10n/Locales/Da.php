<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class Da extends Locale
{
    use Rule1;

    protected $isoCode = 'da';
    protected $englishName = 'Danish';
    protected $nativeName = 'dansk';
    protected $isRtl = false;
}
