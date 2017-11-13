<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule15;

class Is extends Locale
{
    use Rule15;

    protected $isoCode = 'is';
    protected $englishName = 'Icelandic';
    protected $nativeName = 'Íslenska';
    protected $isRtl = false;
}
