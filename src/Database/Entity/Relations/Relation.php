<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Database\Entity\Relations;

use Sydes\Database\Entity\Builder;
use Sydes\Database\Entity\Collection;
use Sydes\Database\Entity\Model;
use Sydes\Database\Query\Builder as QueryBuilder;
use Sydes\Database\Query\Expression;

abstract class Relation
{
    /**
     * The Eloquent query builder instance.
     *
     * @var Builder
     */
    protected $query;

    /**
     * The parent model instance.
     *
     * @var Model
     */
    protected $parent;

    /**
     * The related model instance.
     *
     * @var Model
     */
    protected $related;

    /**
     * Indicates if the relation is adding constraints.
     *
     * @var bool
     */
    protected static $constraints = true;

    /**
     * Create a new relation instance.
     *
     * @param Builder $query
     * @param Model  $parent
     */
    public function __construct(Builder $query, Model $parent)
    {
        $this->query = $query;
        $this->parent = $parent;
        $this->related = $query->getModel();

        $this->addConstraints();
    }

    /**
     * Run a callback with constraints disabled on the relation.
     *
     * @param \Closure $callback
     * @return mixed
     */
    public static function noConstraints(\Closure $callback)
    {
        $previous = static::$constraints;

        static::$constraints = false;

        // When resetting the relation where clause, we want to shift the first element
        // off of the bindings, leaving only the constraints that the developers put
        // as "extra" on the relationships, and not original relation constraints.
        try {
            return call_user_func($callback);
        } finally {
            static::$constraints = $previous;
        }
    }

    /**
     * Set the base constraints on the relation query.
     */
    abstract public function addConstraints();

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param Model[] $models
     */
    abstract public function addEagerConstraints(array $models);

    /**
     * Initialize the relation on a set of models.
     *
     * @param Model[] $models
     * @param string  $relation
     * @return Model[]
     */
    abstract public function initRelation(array $models, $relation);

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param Model[]    $models
     * @param Collection $results
     * @param string     $relation
     * @return Model[]
     */
    abstract public function match(array $models, Collection $results, $relation);

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    abstract public function getResults();

    /**
     * Get the relationship for eager loading.
     *
     * @return Collection
     */
    public function getEager()
    {
        return $this->get();
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param array $columns
     * @return Collection
     */
    public function get($columns = ['*'])
    {
        return $this->query->get($columns);
    }

    /**
     * Add the constraints for a relationship count query.
     *
     * @param Builder $query
     * @param Builder $parentQuery
     * @return Builder
     */
    public function getRelationExistenceCountQuery(Builder $query, Builder $parentQuery)
    {
        return $this->getRelationExistenceQuery(
            $query, $parentQuery, new Expression('count(*)')
        );
    }

    /**
     * Add the constraints for an internal relationship existence query.
     *
     * Essentially, these queries compare on column names like whereColumn.
     *
     * @param Builder     $query
     * @param Builder     $parentQuery
     * @param array|mixed $columns
     * @return Builder
     */
    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        return $query->select($columns)->whereColumn(
            $this->getQualifiedParentKeyName(), '=', $this->getExistenceCompareKey()
        );
    }

    /**
     * Get all of the primary keys for an array of models.
     *
     * @param Model[] $models
     * @param string  $key
     * @return array
     */
    protected function getKeys(array $models, $key = null)
    {
        return collect($models)->map(function (Model $value) use ($key) {
            return $key ? $value->getAttribute($key) : $value->getKey();
        })->values()->unique()->sort()->all();
    }

    /**
     * Get the underlying query for the relation.
     *
     * @return Builder
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Get the base query builder driving the Eloquent builder.
     *
     * @return QueryBuilder
     */
    public function getBaseQuery()
    {
        return $this->query->getQuery();
    }

    /**
     * Get the parent model of the relation.
     *
     * @return Model
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get the fully qualified parent key name.
     *
     * @return string
     */
    public function getQualifiedParentKeyName()
    {
        return $this->parent->getQualifiedKeyName();
    }

    /**
     * Get the related model of the relation.
     *
     * @return Model
     */
    public function getRelated()
    {
        return $this->related;
    }

    /**
     * Handle dynamic method calls to the relationship.
     *
     * @param string $method
     * @param array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $result = call_user_func_array([$this->query, $method], $parameters);

        if ($result === $this->query) {
            return $this;
        }

        return $result;
    }

    /**
     * Force a clone of the underlying query builder when cloning.
     */
    public function __clone()
    {
        $this->query = clone $this->query;
    }
}
