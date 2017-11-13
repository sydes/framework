<?php

namespace Sydes\L10n\Locales;

use Sydes\L10n\Locale;
use Sydes\L10n\Plural\Rule7;

class RuLocale extends Locale
{
    use Rule7;

    protected $isoCode = 'ru';
    protected $englishName = 'Russian';
    protected $nativeName = 'Русский';
    protected $isRtl = false;
    
    public function date($format, $timestamp = null)
    {
        $translate = [
            'am' => 'дп', 'pm' => 'пп', 'AM' => 'ДП', 'PM' => 'ПП',
            'Monday'    => 'Понедельник', 'Mon' => 'Пн',
            'Tuesday'   => 'Вторник',     'Tue' => 'Вт',
            'Wednesday' => 'Среда',       'Wed' => 'Ср',
            'Thursday'  => 'Четверг',     'Thu' => 'Чт',
            'Friday'    => 'Пятница',     'Fri' => 'Пт',
            'Saturday'  => 'Суббота',     'Sat' => 'Сб',
            'Sunday'    => 'Воскресенье', 'Sun' => 'Вс',
            'Jan' => 'Янв', 'Feb' => 'Фев', 'Mar' => 'Мар', 'Apr' => 'Апр', 'Jun' => 'Июн',
            'Jul' => 'Июл', 'Aug' => 'Авг', 'Sep' => 'Сен', 'Oct' => 'Окт', 'Nov' => 'Ноя', 'Dec' => 'Дек',
            'st' => 'ое', 'nd' => 'ое', 'rd' => 'е', 'th' => 'ое',
        ];

        $months = strpos($format, 'F') === 0 ? [
            'January'   => 'Январь',   'February' => 'Февраль', 'March'    => 'Март',   'April'    => 'Апрель',
            'May'       => 'Май',      'June'     => 'Июнь',    'July'     => 'Июль',   'August'   => 'Август',
            'September' => 'Сентябрь', 'October'  => 'Октябрь', 'November' => 'Ноябрь', 'December' => 'Декабрь',
        ] : [
            'January'   => 'Января',   'February' => 'Февраля', 'March'    => 'Марта',  'April'    => 'Апреля',
            'May'       => 'Мая',      'June'     => 'Июня',    'July'     => 'Июля',   'August'   => 'Августа',
            'September' => 'Сентября', 'October'  => 'Октября', 'November' => 'Ноября', 'December' => 'Декабря',
        ];

        $date = is_null($timestamp) ? date($format) : date($format, $timestamp);
        $translate = array_merge($translate, $months);

        return strtr($date, $translate);
    }
}
