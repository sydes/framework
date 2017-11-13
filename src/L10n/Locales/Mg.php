<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule2;

class Mg extends Locale
{
    use Rule2;

    protected $isoCode = 'mg';
    protected $englishName = 'Malagasy';
    protected $nativeName = 'Malagasy fiteny';
    protected $isRtl = false;
}
