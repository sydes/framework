<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Database\Entity;

use Sydes\Database\Query\Builder as QueryBuilder;
use Sydes\Http\Request;

class Repository
{
    use Concerns\InteractsWithModel;

    /**
     * @var Manager
     */
    protected $em;

    public function __construct(Manager $em)
    {
        $this->em = $em;

        if ($this->entity) {
            $this->forEntity($this->entity);
        }
    }

    /**
     * Get all of the models from the database.
     *
     * @param array $columns
     * @return Collection
     */
    public function all($columns = ['*'])
    {
        return $this->newQuery()->get($columns);
    }

    /**
     * Save the model to the database
     *
     * @param Model $model
     * @return bool
     */
    public function save(Model $model)
    {
        return $this->em->save($model);
    }

    /**
     * Delete the model from the database
     *
     * @param Model $model
     * @return int
     */
    public function delete(Model $model)
    {
        return $this->em->delete($model);
    }

    /**
     * Destroy the models for the given IDs.
     *
     * @param  array|int  $ids
     * @return int
     */
    public function destroy($ids)
    {
        $ids = is_array($ids) ? $ids : func_get_args();
        $key = $this->model->getKeyName();

        return $this->newQuery()->whereIn($key, $ids)->delete();
    }

    /**
     * Begin querying a model with eager loading.
     *
     * @param array|string $relations
     * @return Builder
     */
    public function with($relations)
    {
        return $this->newQuery()->with(
            is_string($relations) ? func_get_args() : $relations
        );
    }

    /**
     * Filter and sort result by data from request
     *
     * @param Request $req
     * @return Builder
     */
    public function filteredAndSorted(Request $req)
    {
        $query = $this->newQuery();

        foreach ($req->input('filter', []) as $field => $criterion) {
            if ($this->model->hasField($field) && ($filter = $this->parseCriterion($criterion)) !== null) {
                $query = $this->applyFilter($field, $filter, $query);
            }
        }

        if ($req->has('by')) {
            $query = $query->orderBy($req->input('by'), $req->input('order', 'desc'));
        } else {
            $query = $query->orderByDesc($this->model->getKeyName());
        }

        return $query;
    }

    protected function parseCriterion($value){
        $ret = null;

        if (!preg_match("/([!<>]?[*>=]?) ?([{}\w ,'-]+) ?(\*?)/iu", $value, $out)) {
            return $ret;
        }

        if (empty($out[1])) {
            if (strpos($out[2], ',') !== false) {
                $ret = ['in'];
                foreach (explode(',', $out[2]) as $item) {
                    $ret[1][] = trim($item);
                }
            } elseif (strpos($out[2], '-') !== false) {
                $val = explode('-', $out[2], 2);
                $val[0] = trim($val[0]);
                $val[1] = trim($val[1]);
                if (is_numeric($val[0]) && is_numeric($val[1])) {
                    $ret = ['between', $val];
                }
            } elseif ($out[3] == '*') {
                $ret = ['begins_with', $out[2]];
            } else {
                $ret = ['equal', $out[2]];
            }
        } elseif ($out[1] == '=') {
            $ret = ['equal', $out[2]];
            if ($out[2] == "''") {
                $ret[0] = 'is_empty';
            }
        } elseif ($out[1] == '!=' || $out[1] == '<>') {
            $ret = ['not_equal', $out[2]];
            if ($out[2] == "''") {
                $ret[0] = 'is_not_empty';
            }
        } elseif ($out[1] == '!') {
            if (strpos($out[2], ',') !== false) {
                $ret = ['not_in'];
                foreach (explode(',', $out[2]) as $item) {
                    $ret[1][] = trim($item);
                }
            } elseif (strpos($out[2], '-') !== false) {
                $val = explode('-', $out[2], 2);
                $val[0] = trim($val[0]);
                $val[1] = trim($val[1]);
                if (is_numeric($val[0]) && is_numeric($val[1])) {
                    $ret = ['not_between', $val];
                }
            } elseif ($out[3] == '*') {
                $ret = ['not_begins_with', $out[2]];
            } else {
                $ret = ['not_equal', $out[2]];
            }
        } elseif ($out[1] == '<') {
            $ret = ['less', $out[2]];
        } elseif ($out[1] == '<=') {
            $ret = ['less_or_equal', $out[2]];
        } elseif ($out[1] == '>') {
            $ret = ['greater', $out[2]];
        } elseif ($out[1] == '>=') {
            $ret = ['greater_or_equal', $out[2]];
        } elseif ($out[1] == '*') {
            if ($out[3] == '*') {
                $ret = ['contains', $out[2]];
            } else {
                $ret = ['ends_with', $out[2]];
            }
        } elseif ($out[1] == '!*') {
            if ($out[3] == '*') {
                $ret = ['not_contains', $out[2]];
            } else {
                $ret = ['not_ends_with', $out[2]];
            }
        }

        return $ret;
    }

    /**
     * Apply constraints to query
     *
     * @param string  $field
     * @param array   $filter
     * @param Builder $query
     * @param string  $boolean
     * @return Builder
     */
    protected function applyFilter($field, array $filter, Builder $query, $boolean = 'and') {
        $value = $filter[1];

        switch ($filter[0]) {
            case 'equal':
                $query->where($field, '=', $value, $boolean);
                break;
            case 'not_equal':
                $query->where($field, '<>', $value, $boolean);
                break;
            case 'in':
                $query->whereIn($field, (array)$value, $boolean);
                break;
            case 'not_in':
                $query->whereNotIn($field, (array)$value, $boolean);
                break;
            case 'less':
                $query->where($field, '<', $value, $boolean);
                break;
            case 'less_or_equal':
                $query->where($field, '<=', $value, $boolean);
                break;
            case 'greater':
                $query->where($field, '>', $value, $boolean);
                break;
            case 'greater_or_equal':
                $query->where($field, '>=', $value, $boolean);
                break;
            case 'between':
                $query->whereBetween($field, (array)$value, $boolean);
                break;
            case 'not_between':
                $query->whereNotBetween($field, (array)$value, $boolean);
                break;
            case 'begins_with':
                $query->where($field, 'like', "{$value}%", $boolean);
                break;
            case 'not_begins_with':
                $query->where($field, 'not like', "{$value}%", $boolean);
                break;
            case 'contains':
                $query->where($field, 'like', "%{$value}%", $boolean);
                break;
            case 'not_contains':
                $query->where($field, 'not like', "%{$value}%", $boolean);
                break;
            case 'ends_with':
                $query->where($field, 'like', "%{$value}", $boolean);
                break;
            case 'not_ends_with':
                $query->where($field, 'not like', "%{$value}", $boolean);
                break;
            case 'is_empty':
                $query->where($field, '=', '', $boolean);
                break;
            case 'is_not_empty':
                $query->where($field, '<>', '', $boolean);
                break;
            case 'is_null':
                $query->whereNull($field, $boolean);
                break;
            case 'is_not_null':
                $query->whereNotNull($field, $boolean);
                break;
        }

        return $query;
    }

    /**
     * Get a new query builder for the model's table.
     *
     * @return Builder
     */
    protected function newQuery()
    {
        if (!$this->model) {
            throw new \RuntimeException("Entity not set in this repository");
        }

        $builder = new Builder(new QueryBuilder($this->em->getConnection()));

        return $builder->setModel($this->model);
    }

    /**
     * Handle dynamic method calls into the repository.
     *
     * @param string $method
     * @param array  $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->newQuery(), $method], $args);
    }
}
