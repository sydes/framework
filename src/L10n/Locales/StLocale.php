<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class StLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'st';
    protected $englishName = '';
    protected $nativeName = 'seSotho';
    protected $isRtl = false;
}
