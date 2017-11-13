<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class LbLocale extends Locale
{
    use Rule1;

    protected $isoCode = 'lb';
    protected $englishName = 'Luxembourgish';
    protected $nativeName = 'Lëtzebuergesch';
    protected $isRtl = false;
}
