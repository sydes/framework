<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule12;

class ArLocale extends Locale
{
    use Rule12;

    protected $isoCode = 'ar';
    protected $englishName = 'Arabic';
    protected $nativeName = '‫العربية‬';
    protected $isRtl = true;
}
