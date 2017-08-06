<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Database\Entity\Concerns;

use Sydes\Database\Entity\Model;

trait InteractsWithModel
{
    /**
     * Class name of associated entity
     *
     * @var string
     */
    protected $entity;

    /**
     * Instance of entity
     *
     * @var Model
     */
    protected $model;

    /**
     * Set class name of entity
     *
     * @param string $class class name
     * @return $this
     */
    public function forEntity($class)
    {
        $this->entity = $class;
        $this->model = new $class;

        return $this;
    }

    /**
     * Set instance of entity
     *
     * @param Model $model
     * @return $this
     */
    public function setModel(Model $model)
    {
        $this->entity = get_class($model);
        $this->model = $model;

        return $this;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }
}
