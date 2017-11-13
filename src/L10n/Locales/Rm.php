<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class Rm extends Locale
{
    use Rule1;

    protected $isoCode = 'rm';
    protected $englishName = '';
    protected $nativeName = 'rumantsch grischun';
    protected $isRtl = false;
}
