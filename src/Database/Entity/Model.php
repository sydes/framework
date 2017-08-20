<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Database\Entity;

use Sydes\Database\Entity\Relations\Relation;

class Model
{
    use Concerns\HasTableInfo,
        Concerns\HasFields,
        Concerns\HasAttributes,
        Concerns\HasRelationships;

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
     * Query builder for relations
     *
     * @var Builder
     */
    protected static $query;

    /**
     * @var array
     */
    protected static $locales = [];

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
     * Set the specific relationship in the model.
     *
     * @param string $name
     * @param mixed  $value
     * @return $this
     */
    public function setRelationResult($name, $value)
    {
        $this->field($name)->set($value);

        return $this;
    }

    /**
     * @param Model  $related
     * @param string $name
     * @param array  $settings
     * @return Relation
     */
    protected function createRelation(Model $related, $name, $settings)
    {
        $relation = camel_case($settings['relation']);

        switch ($relation) {
            case 'hasOne':
            case 'hasMany':
                return $this->{$relation}(
                    $this->newQueryFor($related), $settings['on_key'], $this->getKeyName(), $this->getkey()
                );
            case 'belongsTo':
                return $this->belongsTo(
                    $this->newQueryFor($related), $name, $settings['on_key'], $this->getAttribute($name)
                );
            default:
                $pivot = $this->joiningTable($this->getTableSingular(), $related->getTableSingular());

                return $this->belongsToMany($this->newQueryFor($related), $this)
                    ->orderBy($pivot.'.position', 'asc');
        }
    }

    /**
     * @param Model $model
     * @return Builder
     */
    public function newQueryFor(Model $model)
    {
        return self::$query->newQuery()->setModel($model);
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
     * @param Builder $query
     */
    public static function setQuery(Builder $query)
    {
        self::$query = $query;
    }

    /**
     * @param array $locales
     */
    public static function setLocales(array $locales)
    {
        self::$locales = $locales;
    }

    /**
     * @return array
     */
    public static function getLocales()
    {
        return self::$locales;
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
        return isset($this->attributes[$key]);
    }

    /**
     * @param string $key
     */
    public function __unset($key)
    {
        $this->setAttribute($key, '');
    }
}
