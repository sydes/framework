<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class GuLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'gu';
    protected $englishName = 'Gujarati';
    protected $nativeName = 'ગુજરાતી';
    protected $isRtl = false;
}
