<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule0;

class Id extends Locale
{
    use Rule0;

    protected $isoCode = 'id';
    protected $englishName = 'Indonesian';
    protected $nativeName = 'Bahasa Indonesia';
    protected $isRtl = false;
}
