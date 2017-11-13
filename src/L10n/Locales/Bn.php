<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class Bn extends Locale
{
    use Rule1;

    protected $isoCode = 'bn';
    protected $englishName = 'Bengali';
    protected $nativeName = 'বাংলা';
    protected $isRtl = false;
}
