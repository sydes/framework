<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class Hi extends Locale
{
    use Rule1;

    protected $isoCode = 'hi';
    protected $englishName = 'Hindi';
    protected $nativeName = 'हिन्दी';
    protected $isRtl = false;
}
