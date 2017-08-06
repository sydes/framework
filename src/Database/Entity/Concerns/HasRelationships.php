<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Database\Entity\Concerns;

use Sydes\Database\Entity\Builder;
use Sydes\Database\Entity\Model;
use Sydes\Database\Entity\Relations\BelongsTo;
use Sydes\Database\Entity\Relations\BelongsToMany;
use Sydes\Database\Entity\Relations\HasMany;
use Sydes\Database\Entity\Relations\HasOne;

trait HasRelationships
{
    /**
     * Define a one-to-one relationship.
     *
     * @param Builder $query
     * @param string  $foreignKey
     * @param string  $localKey
     * @param string  $value
     * @return HasOne
     */
    public function hasOne(Builder $query, $foreignKey, $localKey, $value)
    {
        $foreignKey = $foreignKey ?: $this->model->getForeignKey();
        $localKey = $localKey ?: $this->model->getKeyName();

        return new HasOne($query, $this->model, $query->getModel()->getTable().'.'.$foreignKey, $localKey, $value);
    }

    /**
     * Define a one-to-many relationship.
     *
     * @param Builder $query
     * @param string  $foreignKey
     * @param string  $localKey
     * @param string  $value
     * @return HasMany
     */
    public function hasMany(Builder $query, $foreignKey, $localKey, $value)
    {
        $foreignKey = $foreignKey ?: $this->model->getForeignKey();
        $localKey = $localKey ?: $this->model->getKeyName();

        return new HasMany($query, $this->model, $query->getModel()->getTable().'.'.$foreignKey, $localKey, $value);
    }

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @param Builder $query
     * @param string  $foreignKey
     * @param string  $ownerKey
     * @param string  $value
     * @return BelongsTo
     */
    public function belongsTo(Builder $query, $foreignKey, $ownerKey, $value)
    {
        $related = $query->getModel();
        $foreignKey = $foreignKey ?: $related->getForeignKey();
        $ownerKey = $ownerKey ?: $related->getKeyName();

        return new BelongsTo($query, $this->model, $foreignKey, $ownerKey, $value);
    }

    /**
     * Define a many-to-many relationship.
     *
     * @param Builder $query
     * @param Model   $local
     * @return BelongsToMany
     */
    public function belongsToMany(Builder $query, Model $local) {
        $related = $query->getModel();
        $pivot = $this->joiningTable($related->getTableSingular(), $local->getTableSingular());

        return new BelongsToMany(
            $query, $local, $pivot, $local->getForeignKey(), $related->getForeignKey(),
            $local->getKeyName(), $related->getKeyName(), $local->getKey()
        );
    }

    /**
     * Get the joining table name for a many-to-many relation.
     *
     * @param string $related
     * @param string $local
     * @return string
     */
    public function joiningTable($related, $local)
    {
        $models = [$related, $local];

        sort($models);

        return strtolower(implode('_', $models));
    }
}
