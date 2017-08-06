<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Database\Entity;

use Sydes\Support\Collection as BaseCollection;

class Collection extends BaseCollection
{
    public function toPresenter()
    {
        return $this->map(function (Model $model) {
            return $model->toPresenter();
        });
    }
}
