<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule4;

class GdLocale extends Locale
{
    use Rule4;

    protected $isoCode = 'gd';
    protected $englishName = 'Gaelic';
    protected $nativeName = 'Gàidhlig';
    protected $isRtl = false;
}
