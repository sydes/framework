<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class No extends Locale
{
    use Rule1;

    protected $isoCode = 'no';
    protected $englishName = 'Norwegian';
    protected $nativeName = 'Norsk';
    protected $isRtl = false;
}
