<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule6;

class Lt extends Locale
{
    use Rule6;

    protected $isoCode = 'lt';
    protected $englishName = 'Lithuanian';
    protected $nativeName = 'lietuvių kalba';
    protected $isRtl = false;
}
