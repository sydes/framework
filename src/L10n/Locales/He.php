<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class He extends Locale
{
    use Rule1;

    protected $isoCode = 'he';
    protected $englishName = 'Hebrew';
    protected $nativeName = '‫עברית‬';
    protected $isRtl = true;
}
