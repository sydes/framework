<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Database\Entity;

class Model
{
    use Concerns\HasTableInfo,
        Concerns\HasFields,
        Concerns\HasAttributes;

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 15;

    /**
     * Indicates if the model exists in database.
     *
     * @var bool
     */
    private $exists = false;

    /**
     * Booted relationships
     *
     * @var array
     */
    protected $relations = [];

    public function __construct(array $attrs = [])
    {
        $this->fill($attrs);
    }

    /**
     * Fill the model with an array of attributes
     *
     * @param array $attrs
     * @return $this
     */
    public function fill(array $attrs)
    {
        foreach (array_intersect_key($attrs, $this->fields) as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Get raw data from fields
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Convert the model's data to an array for inserting to database
     *
     * @return array
     */
    public function toStorage()
    {
        $data = [];
        foreach ($this->getFields() as $name => $field) {
            if (($value = $field->toString()) !== null) {
                $data[$name] = $value;
            }
        }

        return $data;
    }

    /**
     * Convert the entity to its representation based on settings.
     *
     * @param string $class
     * @return Presenter
     */
    public function toPresenter($class = null)
    {
        return ($class) ? new $class($this) : new Presenter($this);
    }

    /**
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * @param int $perPage
     * @return $this
     */
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return $this->exists;
    }

    /**
     * @param bool $exists
     * @return $this
     */
    public function setExists($exists)
    {
        $this->exists = $exists;

        return $this;
    }

    /**
     * @param array|\stdClass $attrs
     * @return Model
     */
    public function newFromStorage($attrs)
    {
        $model = clone $this;
        $model->bootedFields = [];
        $model->booted = false;

        foreach ((array)$attrs as $key => $value) {
            $model->attributes[$key] = $value;
        }

        return $model->setExists(true);
    }

    public function cloneWith($attrs = [])
    {
        $model = clone $this;

        return $model->fill($attrs);
    }

    /**
     * Get all the booted relations for the instance.
     *
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * Get a specified relationship.
     *
     * @param  string  $relation
     * @return mixed
     */
    public function getRelation($relation)
    {
        return $this->relations[$relation];
    }

    /**
     * Determine if the given relation is booted.
     *
     * @param  string  $key
     * @return bool
     */
    public function relationBooted($key)
    {
        return array_key_exists($key, $this->relations);
    }

    /**
     * Set the specific relationship in the model.
     *
     * @param  string  $relation
     * @param  mixed  $value
     * @return $this
     */
    public function setRelationResult($relation, $value)
    {
        $this->field($relation)->set($value);

        return $this;
    }

    /**
     * Set the relationship in field.
     *
     * @param  string  $relation
     * @param  mixed  $value
     * @return $this
     */
    public function setRelation($relation, $value)
    {
        $this->field($relation)->setRelation($value);

        return $this;
    }

    /**
     * Set the specific relationship in the model.
     *
     * @param  string  $relation
     * @param  mixed  $value
     * @return $this
     */
    public function bootRelation($relation, $value)
    {
        $this->relations[$relation] = $value;

        return $this;
    }

    /**
     * Set the entire relations array on the model.
     *
     * @param  array  $relations
     * @return $this
     */
    public function setRelations(array $relations)
    {
        $this->relations = $relations;

        return $this;
    }

    /**
     * @param Event $events
     * @return Event
     */
    public function fillEvents(Event $events)
    {
        foreach ($this->getFields() as $field) {
            $field->getEventListeners($events);
        }

        return $events;
    }

    /**
     * Create entity with structure from array
     *
     * @param array $state
     * @return static
     */
    public static function unserialize(array $state)
    {
        $m = new static;

        if (isset($state['table'])) {
            $m->setTable($state['table']);
        }

        if (isset($state['fields'])) {
            $m->setFields($state['fields']);
        }

        return $m;
    }

    /**
     * Convert entity structure to array
     *
     * @return array
     */
    public function serialize()
    {
        return [
            'table' => $this->getTable(),
            'fields' => $this->getFieldList(),
        ];
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * @param string $key
     * @param string $value
     * @return Model
     */
    public function __set($key, $value)
    {
        return $this->setAttribute($key, $value);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->hasField($key);
    }

    /**
     * @param string $key
     */
    public function __unset($key)
    {
        $this->setAttribute($key, '');
    }
}
