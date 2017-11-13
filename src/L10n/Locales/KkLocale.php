<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule0;

class KkLocale extends Locale
{
    use Rule0;

    protected $isoCode = 'kk';
    protected $englishName = 'Kazakh';
    protected $nativeName = 'Қазақ тілі';
    protected $isRtl = false;
}
