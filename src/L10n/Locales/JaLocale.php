<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule0;

class JaLocale extends Locale
{
    use Rule0;

    protected $isoCode = 'ja';
    protected $englishName = 'Japanese';
    protected $nativeName = '日本語';
    protected $isRtl = false;
}
