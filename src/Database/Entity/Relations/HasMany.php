<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Database\Entity\Relations;

use Sydes\Database\Entity\Collection;
use Sydes\Database\Entity\Model;

class HasMany extends HasOneOrMany
{
    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->query->get();
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
            $model->setRelationResult($relation, new Collection);
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
        return $this->matchOneOrMany($models, $results, $relation, 'many');
    }
}
