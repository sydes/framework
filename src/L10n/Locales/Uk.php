<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule7;

class Uk extends Locale
{
    use Rule7;

    protected $isoCode = 'uk';
    protected $englishName = 'Ukrainian';
    protected $nativeName = 'українська мова';
    protected $isRtl = false;
}
