<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule0;

class ZhLocale extends Locale
{
    use Rule0;

    protected $isoCode = 'zh';
    protected $englishName = 'Chinese';
    protected $nativeName = '中文、汉语、漢語';
    protected $isRtl = false;
}
