<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule2;

class MiLocale extends Locale
{
    use Rule2;

    protected $isoCode = 'mi';
    protected $englishName = '';
    protected $nativeName = 'te reo Māori';
    protected $isRtl = false;
}
