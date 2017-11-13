<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class EuLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'eu';
    protected $englishName = 'Basque';
    protected $nativeName = 'euskara';
    protected $isRtl = false;
}
