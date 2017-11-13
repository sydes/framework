<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class Nl extends Locale
{
    use Rule1;

    protected $isoCode = 'nl';
    protected $englishName = 'Dutch';
    protected $nativeName = 'Nederlands';
    protected $isRtl = false;
}
