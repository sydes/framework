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

class BelongsTo extends Relation
{
    /**
     * The child model instance of the relation.
     */
    protected $child;

    /**
     * The foreign key of the parent model.
     *
     * @var string
     */
    protected $foreignKey;

    /**
     * The associated key on the parent model.
     *
     * @var string
     */
    protected $ownerKey;

    /**
     * @var string
     */
    protected $value;

    /**
     * The count of self joins.
     *
     * @var int
     */
    protected static $selfJoinCount = 0;

    /**
     * Create a new belongs to relationship instance.
     *
     * @param Builder $query
     * @param Model   $child
     * @param string  $foreignKey
     * @param string  $ownerKey
     * @param string  $value
     */
    public function __construct(Builder $query, Model $child, $foreignKey, $ownerKey, $value)
    {
        $this->ownerKey = $ownerKey;
        $this->foreignKey = $foreignKey;
        $this->value = $value;
        $this->child = $child;

        parent::__construct($query, $child);
    }

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->query->first();
    }

    /**
     * Set the base constraints on the relation query.
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            $this->query->where($this->getQualifiedOwnerKeyName(), '=', $this->value);
        }
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param Model[] $models
     */
    public function addEagerConstraints(array $models)
    {
        $this->query->whereIn($this->getQualifiedOwnerKeyName(), $this->getEagerModelKeys($models));
    }

    /**
     * Gather the keys from an array of related models.
     *
     * @param Model[] $models
     * @return array
     */
    protected function getEagerModelKeys(array $models)
    {
        $keys = [];

        foreach ($models as $model) {
            if ($value = $model->{$this->foreignKey}) {
                $keys[] = $value;
            }
        }

        if (count($keys) === 0) {
            return [null];
        }

        sort($keys);

        return array_values(array_unique($keys));
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param Model[] $models
     * @param string  $relation
     * @return Model[]
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelationResult($relation, null);
        }

        return $models;
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param Model[]    $models
     * @param Collection $results
     * @param string     $relation
     * @return Model[]
     */
    public function match(array $models, Collection $results, $relation)
    {
        $dictionary = [];

        foreach ($results as $result) {
            $dictionary[$result->{$this->ownerKey}] = $result;
        }

        foreach ($models as $model) {
            if (isset($dictionary[$model->{$this->foreignKey}])) {
                $model->setRelationResult($relation, $dictionary[$model->{$this->foreignKey}]);
            }
        }

        return $models;
    }

    /**
     * Add the constraints for a relationship query.
     *
     * @param Builder     $query
     * @param Builder     $parentQuery
     * @param array|mixed $columns
     * @return Builder
     */
    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        if ($parentQuery->getQuery()->from == $query->getQuery()->from) {
            return $this->getRelationExistenceQueryForSelfRelation($query, $parentQuery, $columns);
        }

        return $query->select($columns)->whereColumn(
            $this->getQualifiedForeignKey(), '=', $query->getModel()->getTable().'.'.$this->ownerKey
        );
    }

    /**
     * Add the constraints for a relationship query on the same table.
     *
     * @param Builder     $query
     * @param Builder     $parentQuery
     * @param array|mixed $columns
     * @return Builder
     */
    public function getRelationExistenceQueryForSelfRelation(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        $query->select($columns)->from(
            $query->getModel()->getTable().' as '.$hash = $this->getRelationCountHash()
        );

        $query->getModel()->setTable($hash);

        return $query->whereColumn(
            $hash.'.id', '=', $this->getQualifiedForeignKey()
        );
    }

    /**
     * Get a relationship join table hash.
     *
     * @return string
     */
    public function getRelationCountHash()
    {
        return 'laravel_reserved_'.static::$selfJoinCount++;
    }

    /**
     * Get the fully qualified foreign key of the relationship.
     *
     * @return string
     */
    public function getQualifiedForeignKey()
    {
        return $this->child->getTable().'.'.$this->foreignKey;
    }

    /**
     * Get the fully qualified associated key of the relationship.
     *
     * @return string
     */
    public function getQualifiedOwnerKeyName()
    {
        return $this->related->getTable().'.'.$this->ownerKey;
    }
}
