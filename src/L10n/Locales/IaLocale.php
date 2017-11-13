<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class IaLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'ia';
    protected $englishName = 'Interlingua';
    protected $nativeName = 'Interlingua';
    protected $isRtl = false;
}
