<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule0;

class LoLocale extends Locale
{
    use Rule0;

    protected $isoCode = 'lo';
    protected $englishName = 'Lao';
    protected $nativeName = 'ພາສາລາວ';
    protected $isRtl = false;
}
