<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Database\Entity;

class Presenter
{
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get field or his output
     *
     * @param string $key
     * @param array  $args
     * @return mixed|Field
     */
    public function __call($key, $args)
    {
        return empty($args) ? $this->model->field($key) : call_user_func_array([$this->model->field($key), 'output'], $args);
    }

    /**
     * Get fields default output
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->model->field($key)->output();
    }
}
