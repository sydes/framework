<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule1;

class Ps extends Locale
{
    use Rule1;

    protected $isoCode = 'ps';
    protected $englishName = 'Pashto';
    protected $nativeName = '‫پښتو‬';
    protected $isRtl = false;
}
