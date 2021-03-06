<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Html;

use Psr\Http\Message\RequestInterface;

class BS4 extends Base
{
    /**
    * {@inheritdoc}
    */
    public static function a($text, $url = false, array $attr = [])
    {
        if (isset($attr['button'])) {
            $attr = static::makeButton($attr);
        }

        return parent::a($text, $url, $attr);
    }

    /**
     * {@inheritdoc}
     */
    public static function input($type, $name, $value = '', array $attr = [])
    {
        if ($type == 'file') {
            $attr['class'][] = 'form-control-file';
        } elseif (in_array($type, ['text', 'password', 'datetime-local', 'date', 'month',
            'time', 'week', 'number', 'email', 'url', 'search', 'tel', 'color'])) {
            $attr['class'][] = 'form-control';
        }

        $prefix = '';
        if (isset($attr['prefix'])) {
            $texts = (array)array_remove($attr, 'prefix');
            $prefix .= static::inputAddons($texts);
        }

        $suffix = '';
        if (isset($attr['suffix'])) {
            $texts = (array)array_remove($attr, 'suffix');
            $suffix .= static::inputAddons($texts);
        }

        $groupClass = ['input-group'];

        if (isset($attr['size'])) {
            $size = array_remove($attr, 'size');
            if ($prefix || $suffix) {
                $groupClass[] = 'input-group-'.$size;
            } else {
                $attr['class'][] = 'form-control-'.$size;
            }
        }

        if ($prefix || $suffix) {
            $prefix = static::beginTag('div', ['class' => $groupClass]).$prefix;
            $suffix .= '</div>';
        }

        $input = parent::input($type, $name, $value, $attr);

        return $prefix.$input.$suffix;
    }

    protected static function inputAddons($texts)
    {
        $html = '';
        foreach ($texts as $text) {
            $btn = strpos($text, 'btn') !== false ? 'btn' : 'addon';
            $html .= static::tag('span', $text, ['class' => 'input-group-'.$btn]);
        }

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public static function select($name, $value, array $items, array $attr = [])
    {
        $attr['class'][] = 'form-control';

        return parent::select($name, $value, $items, $attr);
    }

    /**
     * {@inheritdoc}
     */
    public static function textarea($name, $value, array $attr = [])
    {
        $attr['class'][] = 'form-control';

        return parent::textarea($name, $value, $attr);
    }

    public static function button($label = 'Button', array $attr = [])
    {
        $attr = static::makeButton($attr);

        return parent::button($label, $attr);
    }

    /**
     * @param string $label
     * @param string $input
     * @param string $help
     * @return string
     */
    public static function formGroup($label, $input, $help = '')
    {
        $help = $help ? static::tag('small', $help, ['class'=>'form-text text-muted']) : '';

        return '<div class="form-group"><label>'.$label.'</label>'.$input.$help.'</div>';
    }

    /**
     * @param array $crumbs with ['title' => '', 'url' => '']
     * @return string
     */
    public static function breadcrumb($crumbs)
    {
        $html = '';
        foreach ($crumbs as $crumb) {
            if (isset($crumb['url'])) {
                $html .= '<li class="breadcrumb-item"><a href="'.$crumb['url'].'">'.$crumb['title'].'</a></li>';
            } else {
                $html .= '<li class="breadcrumb-item active">'.$crumb['title'].'</li>';
            }
        }

        return '<ol class="breadcrumb">'.$html.'</ol>';
    }

    /**
     * @param RequestInterface $request
     * @param int              $total    Total pages
     * @param int              $maxLinks Max width of pagination, odd number is better
     * @param bool             $arrows   To show links for next page?
     * @param array            $text     Texts for additional links
     * @param string           $class    Html class for container
     * @return string
     */
    public static function pagination(RequestInterface $request, $total, $maxLinks = 7,
        $arrows = false, array $text = [], $class = 'pagination')
    {
        if ($total < 2) {
            return '';
        }

        $text = array_merge([
            'first' => '&laquo;',
            'prev' => '&lsaquo;',
            'next' => '&rsaquo;',
            'last' => '&raquo;',
        ], $text);

        $uri = $request->getUri();
        $path = $uri->getPath();
        parse_str($uri->getQuery(), $query);

        $page = (int)array_remove($query, 'page', 0);
        if ($page < 1) {
            $page = 1;
        }

        if ($total <= $maxLinks) {
            $from = 1;
            $to = $total;
        } else {
            if ($page < floor($maxLinks / 2) + 1) {
                $from = 1;
                $to = $maxLinks;
            } elseif ($page <= $total - floor($maxLinks / 2)) {
                $from = $page - floor($maxLinks / 2);
                $to = $page + floor($maxLinks / 2);
            } else {
                $from = $total - $maxLinks + 1;
                $to = $total;
            }
        }

        $prev = $next = $first = $last = $all = '';

        for ($i = $from; $i <= $to; $i++) {
            if ($i != $page) {
                $newQuery = $query + ['page' => $i];
                $all .= static::paginationLink($i, $path, $newQuery);
            } else {
                $all .= static::paginationLink($i);
            }
        }

        if ($total > $maxLinks) {
            if ($page > 1) {
                $first = static::paginationLink($text['first'], $path, $query);
            }
            if ($page < $total) {
                $last = static::paginationLink($text['last'], $path, $query + ['page' => $total]);
            }
        }

        if ($arrows && $page > 1) {
            $prev = static::paginationLink($text['prev'], $path, $query + ['page' => $page - 1]);
        }
        if ($arrows && $page < $total) {
            $next = static::paginationLink($text['next'], $path, $query + ['page' => $page + 1]);
        }

        return '<ul class="'.$class.'">'.$first.$prev.$all.$next.$last.'</ul>';
    }

    protected static function paginationLink($title, $path = false, $query = [])
    {
        if ($path) {
            if (isset($query['page']) && $query['page'] == 1) {
                unset($query['page']);
            }

            $query = empty($query) ? '' : '?'.http_build_query($query);

            $act = '';
            $link = '<a class="page-link" href="'.$path.$query.'">'.$title.'</a>';
        } else {
            $act =  ' active';
            $link = '<span class="page-link">'.$title.'</span>';
        }

        return '<li class="page-item'.$act.'">'.$link.'</li>';
    }

    /**
     * @param array  $items
     * @param string $current
     * @param array  $attr
     * @return string
     */
    public static function tabs(array $items, $current = '', array $attr = [])
    {
        static $count = 0;
        $titles = [];
        $contents = '';

        foreach ($items as $key => $d) {
            $active = $current == $key ? ' active' : '';

            $titles[] = [
                'active' => $active,
                'url' => '#tab'.$count.'-'.$key,
                'title' => $d['title'],
                'attr' => [
                    'data-toggle' => 'tab',
                ]
            ];

            $contents .= '<div class="tab-pane'.$active.'" id="tab'.$count.'-'.$key.'">'.$d['content'].'</div>';
        }

        $count++;

        return '<div '.static::attr($attr).'>'.static::nav($titles, 'nav-tabs').
            '<div class="tab-content">'.$contents.'</div></div>';
    }

    /**
     * @param array  $items
     * @param string $style
     * @return string
     */
    public static function nav(array $items, $style = '')
    {
        $html = '';
        foreach ($items as $item) {
            $item['attr']['class'][] = 'nav-link';
            if (!empty($item['active'])) {
                $item['attr']['class'][] = 'active';
            }

            $html .= '<li class="nav-item">'.static::a($item['title'], $item['url'], $item['attr']).'</li>';
        }

        return '<ul class="nav '.$style.'">'.$html.'</ul>';
    }

    /**
     * @param array  $items
     * @param string $current
     * @param bool   $independent Close all other cards on open?
     * @return string
     */
    public static function accordion(array $items, $current = '', $independent = false)
    {
        static $count = 0;
        $html = '';
        $parent = $independent ? '' : 'data-parent=".accordion"';

        foreach ($items as $key => $d) {
            $active = $current == $key ? ' show' : '';
            $html .= '
    <div class="card">
        <div class="card-header" data-toggle="collapse" '.$parent.' data-target="#acc'.$count.'-'.$key.'">
            '.$d['title'].'
        </div>
        <div id="acc'.$count.'-'.$key.'" class="collapse'.$active.'">
            <div class="card-body">'.$d['content'].'</div>
        </div>
    </div>';
        }

        $count++;

        return '<div class="accordion">'.$html.'</div>';
    }

    /**
     * @param array $params
     * @return string
     */
    public static function modal($params)
    {
        if (!isset($params['footer'])) {
            $params['footer'] = '';
        }
        if (!isset($params['size'])) {
            $params['size'] = '';
        }

        $title = '<div class="modal-header"><div class="modal-title">'.$params['title'].'</div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button></div>';
        $body = $params['body'] ? '<div class="modal-body">'.$params['body'].'</div>' : '';
        $footer = $params['footer'] ? '<div class="modal-footer">'.$params['footer'].'</div>' : '';
        $modal = '<div class="modal fade" id="'.$params['id'].'" tabindex="-1" role="dialog">'.
            '<div class="modal-dialog '.$params['size'].'" role="document">'.
            '<div class="modal-content">'.$title.$body.$footer.'</div></div></div>';

        return $modal;
    }

    /**
     * @param array      $items
     * @param array|bool $label
     * @param array      $align
     * @return string
     */
    public static function dropdown(array $items, $label = false, $align = ['right', 'down'])
    {
        if (!$label) {
            $first = array_shift($items);

            $attr = static::makeButton(ifsetor($first['attr'], []), $used);

            if (isset($first['url'])) {
                $action = static::a($first['label'], $first['url'], $attr);
            } else {
                $attr['type'] = ifsetor($first['type'], 'button');
                $action = static::button($first['label'], $attr);
            }

            $drop = static::button('', [
                'class' => ['btn', $used['button'], $used['size'], 'dropdown-toggle', 'dropdown-toggle-split'],
                'data-toggle' => 'dropdown',
            ]);

            $toggle = $action.$drop;
        } else {
            $label['attr'] = static::makeButton($label['attr']);
            $label['attr']['class'][] = 'dropdown-toggle';
            $label['attr']['data-toggle'] = 'dropdown';

            $toggle = static::button($label['label'], $label['attr']);
        }

        $menu = '';
        foreach ($items as $item) {
            $item['attr']['class'][] = 'dropdown-item';

            if (isset($item['url'])) {
                $menu .= static::a($item['label'], $item['url'], $item['attr']);
            } elseif (isset($item['type'])) {
                $item['attr']['type'] = $item['type'];
                $menu .= static::button($item['label'], $item['attr']);
            } elseif (isset($item['divider'])) {
                $menu .= '<div class="dropdown-divider"></div>';
            } elseif (isset($item['header'])) {
                $menu .= '<h6 class="dropdown-header">'.$item['label'].'</h6>';
            } else {
                $menu .= $item['html'];
            }
        }

        $up = in_array('up', $align) ? 'dropup' : '';
        $right = in_array('right', $align) ? 'dropdown-menu-right' : '';

        return '<div class="btn-group '.$up.'">'.$toggle.'<div class="dropdown-menu '.$right.'">'.$menu.'</div></div>';
    }

    protected static function makeButton($attr, &$used = [])
    {
        $class = ['btn'];

        $used['button'] = 'btn-'.array_remove($attr, 'button', 'secondary');
        $class[] = $used['button'];

        $used['size'] = '';
        if (isset($attr['size'])) {
            $used['size'] = 'btn-'.array_remove($attr, 'size');
            $class[] = $used['size'];
        }

        $used['display'] = '';
        if (isset($attr['display'])) {
            $used['display'] = 'btn-'.array_remove($attr, 'display');
            $class[] = $used['display'];
        }

        return static::attrAddClass($attr, $class);
    }

    /**
     * @param string $type
     * @param string $name
     * @param array  $items
     * @param mixed  $selected
     * @param array  $attr
     * @return string
     */
    protected static function optionElement($type, $name, $items, $selected, array $attr = [])
    {
        if (!$items) {
            return '<div>'.t('empty').'</div>';
        }

        $inline = array_remove($attr, 'inline', false);

        $divClass = ['form-check'];
        if ($inline) {
            $divClass[] = 'form-check-inline';
        }

        $html = '';
        foreach ($items as $value => $title) {
            $html .= static::tag('div', static::tag('label', static::input($type, $name, $value, [
                        'checked' => in_array($value, (array)$selected, true),
                        'class' => ['form-check-input'],
                    ]).' '.$title, ['class' => ['form-check-label']]), ['class' => $divClass]);
        }

        return '<div'.static::attr($attr).'>'.$html.'</div>';
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $items List of items 'value' => 'title'
     * @param array  $attr
     * @return string
     */
    public static function checkboxList($name, $value, array $items, array $attr = [])
    {
        $name .= count($items) > 1 ? '[]' : '';

        return static::optionElement('checkbox', $name, $items, $value, $attr);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $items List of items 'value' => 'title'
     * @param array  $attr
     * @return string
     */
    public static function radioList($name, $value, array $items, array $attr = [])
    {
        return static::optionElement('radio', $name, $items, $value, $attr);
    }

    /**
     * @param string   $name
     * @param int|bool $status
     * @return string
     */
    public static function yesNo($name, $status)
    {
        return static::optionElement('radio', $name, ['1' => t('yes'), '0' => t('no')], (int)$status,
            ['inline' => true]);
    }

}
