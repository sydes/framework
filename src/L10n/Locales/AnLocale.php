<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class AnLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'an';
    protected $englishName = 'Aragonese';
    protected $nativeName = 'Aragonés';
    protected $isRtl = false;
}
