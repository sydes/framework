<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Database\Entity;

use Sydes\Database\Concerns\BuildsQueries;
use Sydes\Database\Entity\Relations\Relation;
use Sydes\Database\Query\Builder as QueryBuilder;
use Sydes\Support\Arr;
use Sydes\Support\Str;

/**
 * @method $this where($column, $operator = null, $value = null, $boolean = 'and')
 * @method $this whereBetween($column, array $values, $boolean = 'and', $not = false)
 * @method $this whereNotBetween($column, array $values, $boolean = 'and')
 * @method $this whereNull($column, $boolean = 'and', $not = false)
 * @method $this whereNotNull($column, $boolean = 'and')
 * @method $this whereIn($column, $values, $boolean = 'and', $not = false)
 * @method $this whereNotIn($column, $values, $boolean = 'and')
 * @method $this orderBy($column, $direction = 'asc')
 * @method $this orderByDesc($column)
 */
class Builder
{
    use BuildsQueries;

    /** @var QueryBuilder */
    protected $query;
    /** @var Model */
    protected $model;

    /**
     * The relationships that should be eager loaded.
     *
     * @var array
     */
    protected $eagerLoad = [];

    /**
     * The methods that should be returned from query builder.
     *
     * @var array
     */
    protected $passthru = [
        'insert', 'insertGetId', 'getBindings', 'toSql', 'exists',
        'count', 'min', 'max', 'avg', 'sum', 'getConnection',
    ];

    public function __construct(QueryBuilder $query)
    {
        $this->query = $query;
    }

    /**
     * Find a model by its primary key.
     *
     * @param int|array $id
     * @param array     $columns
     * @return Model|Collection|null
     */
    public function find($id, $columns = ['*'])
    {
        if (is_array($id)) {
            return $this->findMany($id, $columns);
        }

        return $this->where($this->model->getQualifiedKeyName(), $id)->first($columns);
    }

    /**
     * Find multiple models by their primary keys.
     *
     * @param array $ids
     * @param array $columns
     * @return Collection
     */
    public function findMany(array $ids, $columns = ['*'])
    {
        return empty($ids) ? new Collection() : $this->whereIn($this->model->getQualifiedKeyName(), $ids)->get($columns);
    }

    /**
     * Find a model by its primary key or throw an exception.
     *
     * @param int|array $id
     * @param array     $columns
     * @return Model|Collection
     */
    public function findOrFail($id, $columns = ['*'])
    {
        $result = $this->find($id, $columns);

        if (is_array($id)) {
            if (count($result) == count(array_unique($id))) {
                return $result;
            }
        } elseif (!is_null($result)) {
            return $result;
        }

        throw new \RuntimeException('Entity '.get_class($this->model).' with key '.$this->model->getKey().' not found');
    }

    /**
     * Get a single column's value from the first result of a query.
     *
     * @param string $column
     * @return mixed
     */
    public function value($column)
    {
        if ($result = $this->first([$column])) {
            return $result->{$column};
        }

        return null;
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param array $columns
     * @return Collection
     */
    public function get($columns = ['*'])
    {
        if (count($models = $this->getModels($columns)) > 0) {
            $models = $this->eagerLoadRelations($models);
        }

        return new Collection($models);
    }

    /**
     * Get the hydrated models without eager loading.
     *
     * @param array $columns
     * @return Model[]
     */
    public function getModels($columns = ['*'])
    {
        return $this->hydrate(
            $this->query->get($columns)->all()
        );
    }

    /**
     * Create a collection of models from plain arrays.
     *
     * @param Model[] $items
     * @return Model[]
     */
    public function hydrate(array $items)
    {
        return array_map(function ($item) {
            return $this->model->newFromStorage($item);
        }, $items);
    }

    /**
     * Eager load the relationships for the models.
     *
     * @param Model[] $models
     * @return Model[]
     */
    public function eagerLoadRelations(array $models)
    {
        if ($this->model->hasTranslatable()) {
            $models = $this->loadTranslated($models);
        }

        foreach ($this->eagerLoad as $name => $constraints) {
            if (strpos($name, '.') === false) {
                $models = $this->eagerLoadRelation($models, $name, $constraints);
            }
        }

        return $models;
    }

    /**
     * TODO Find a more elegant solution
     *
     * @param Model[] $models
     * @return Model[]
     */
    public function loadTranslated($models)
    {
        $foreign = $this->model->getForeignKey();
        $match = [];

        foreach ($models as $model) {
            $match[$model->getKey()] = $model;
        }

        $translated = $this->query->newQuery()->from($this->model->getTranslationTable())
            ->whereIn($foreign, array_keys($match))->whereIn('locale', Model::getLocales())->get();

        if ($translated) {
            $values = [];
            foreach ($translated as $item) {
                foreach ($this->model->getTranslatableFields() as $field) {
                    $values[$item->{$foreign}][$field][$item->locale] = $item->{$field};
                }
            }

            foreach ($values as $id => $value) {
                foreach ($value as $key => $field) {
                    $match[$id]->setAttribute($key, $field);
                }
            }
        }

        return $models;
    }

    /**
     * Eagerly load the relationship on a set of models.
     *
     * @param array    $models
     * @param string   $name
     * @param \Closure $constraints
     * @return array
     */
    protected function eagerLoadRelation(array $models, $name, \Closure $constraints)
    {
        $relation = $this->getRelation(Arr::first($models), $name);
        $relation->addEagerConstraints($models);
        $constraints($relation);

        return $relation->match($relation->initRelation($models, $name), $relation->getEager(), $name);
    }

    /**
     * Get the relation instance for the given relation name.
     *
     * @param string $name
     * @return Relation
     */
    public function getRelation(Model $model, $name)
    {
        $relation = Relation::noConstraints(function () use ($model, $name) {
            try {
                return $model->getRelationValue($name);
            } catch (\BadMethodCallException $e) {
                throw new \RuntimeException('Call to undefined relationship '.$name);
            }
        });

        $nested = $this->relationsNestedUnder($name);

        if (count($nested) > 0) {
            $relation->getQuery()->with($nested);
        }

        return $relation;
    }

    /**
     * Get the deeply nested relations for a given top-level relation.
     *
     * @param string $relation
     * @return array
     */
    protected function relationsNestedUnder($relation)
    {
        $nested = [];

        foreach ($this->eagerLoad as $name => $constraints) {
            if ($this->isNestedUnder($relation, $name)) {
                $nested[substr($name, strlen($relation.'.'))] = $constraints;
            }
        }

        return $nested;
    }

    /**
     * Determine if the relationship is nested.
     *
     * @param string $relation
     * @param string $name
     * @return bool
     */
    protected function isNestedUnder($relation, $name)
    {
        return Str::contains($name, '.') && Str::startsWith($name, $relation.'.');
    }

    /**
     * Add a generic "order by" clause if the query doesn't already have one.
     */
    protected function enforceOrderBy()
    {
        if (empty($this->query->orders) && empty($this->query->unionOrders)) {
            $this->orderBy($this->model->getQualifiedKeyName(), 'asc');
        }
    }

    /**
     * Set the relationships that should be eager loaded.
     *
     * @param mixed $relations
     * @return $this
     */
    public function with($relations)
    {
        $eagerLoad = $this->parseWithRelations(is_string($relations) ? func_get_args() : $relations);

        $this->eagerLoad = array_merge($this->eagerLoad, $eagerLoad);

        return $this;
    }

    /**
     * Parse a list of relations into individuals.
     *
     * @param array $relations
     * @return array
     */
    protected function parseWithRelations(array $relations)
    {
        $results = [];
        foreach ($relations as $name => $constraints) {
            if (is_numeric($name)) {
                $name = $constraints;
                list($name, $constraints) = Str::contains($name, ':')
                    ? $this->createSelectWithConstraint($name)
                    : [$name, function () {
                        //
                    }];
            }

            $results = $this->addNestedWiths($name, $results);
            $results[$name] = $constraints;
        }

        return $results;
    }

    /**
     * Create a constraint to select the given columns for the relation.
     *
     * @param string $name
     * @return array
     */
    protected function createSelectWithConstraint($name)
    {
        return [explode(':', $name)[0], function ($query) use ($name) {
                $query->select(explode(',', explode(':', $name)[1]));
            }];
    }

    /**
     * Parse the nested relationships in a relation.
     *
     * @param string $name
     * @param array  $results
     * @return array
     */
    protected function addNestedWiths($name, $results)
    {
        $progress = [];

        foreach (explode('.', $name) as $segment) {
            $progress[] = $segment;
            if (!isset($results[$last = implode('.', $progress)])) {
                $results[$last] = function () {
                    //
                };
            }
        }

        return $results;
    }

    /**
     * Get the model instance being queried.
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set a model instance for the model being queried.
     *
     * @param Model $model
     * @return $this
     */
    public function setModel(Model $model)
    {
        $this->model = $model;
        $this->query->from($model->getTable());

        return $this;
    }

    /**
     * Get the underlying query builder instance.
     *
     * @return QueryBuilder
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set the underlying query builder instance.
     *
     * @param QueryBuilder $query
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return static
     */
    public function newQuery()
    {
        return new static($this->query->newQuery());
    }

    /**
     * Dynamically handle calls into the query instance.
     *
     * @param string $method
     * @param array  $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (in_array($method, $this->passthru)) {
            return call_user_func_array([$this->query, $method], $args);
        }

        call_user_func_array([$this->query, $method], $args);

        return $this;
    }

    /**
     * Force a clone of the underlying query builder when cloning.
     */
    public function __clone()
    {
        $this->query = clone $this->query;
    }
}
