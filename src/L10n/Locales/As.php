<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class As extends Locale
{
    use Rule1;

    protected $isoCode = 'as';
    protected $englishName = 'Assamese';
    protected $nativeName = 'অসমীয়া';
    protected $isRtl = false;
}
