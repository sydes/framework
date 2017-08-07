<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Database\Entity;

use Sydes\Database\Connection;
use Sydes\Database\Query\Builder;

class Manager
{
    protected $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * @param string|Model $model
     * @return Repository
     */
    public function getRepository($model)
    {
        $repo = new Repository($this);

        return is_string($model) ? $repo->forEntity($model) : $repo->setModel($model);
    }

    /**
     * @param string|Model $model
     * @return Schema
     */
    public function getSchemaTool($model)
    {
        $schema = new Schema($this);

        return is_string($model) ? $schema->forEntity($model) : $schema->setModel($model);
    }

    /**
     * Save the model to the database
     *
     * @param Model $model
     * @return bool
     */
    public function save(Model $model)
    {
        if ($this->fire($model, 'saving', true) === false) {
            return false;
        }

        $query = $this->conn->table($model->getTable());
        $saved = $model->exists() ? $this->update($query, $model) : $this->insert($query, $model);

        if ($saved) {
            $this->fire($model, 'saved');
            $model->clean();
        }

        return $saved;
    }

    /**
     * Perform a model update operation.
     *
     * @param Builder $query
     * @param Model   $model
     * @return bool
     */
    protected function update(Builder $query, Model $model)
    {
        if ($model->isClean() || $this->fire($model, 'updating', true) === false) {
            return false;
        }

        list($main, $translated) = $this->getDirty($model);

        unset($main[$model->getKeyName()]);
        $query->where($model->getKeyName(), $model->getKey())->update($main);

        foreach ($translated as $t) {
            $this->conn->table($model->getTranslationTable())->updateOrInsert($t['attr'], $t['val']);
        }

        $this->fire($model, 'updated');

        return true;
    }

    /**
     * Perform a model insert operation.
     *
     * @param Builder $query
     * @param Model   $model
     * @return bool
     */
    protected function insert(Builder $query, Model $model)
    {
        if ($this->fire($model, 'inserting', true) === false) {
            return false;
        }

        list($main, $translated) = $this->getDirty($model);

        if ($model->hasIncrementing()) {
            $id = $query->insertGetId($main, $keyName = $model->getKeyName());
            $model->setAttribute($keyName, $id);
        } else {
            if (empty($main)) {
                return true;
            }
            $query->insert($main);
        }

        foreach ($translated as $i => $t) {
            if ($model->hasIncrementing()) {
                $t['attr'][$model->getForeignKey()] = $model->getKey();
            }
            $translated[$i] = array_merge($t['attr'], $t['val']);
        }

        $this->conn->table($model->getTranslationTable())->insert($translated);

        $model->setExists(true);

        $this->fire($model, 'inserted');

        return true;
    }

    private function getDirty(Model $model)
    {
        $main = $model->toStorage();
        $trans = [];

        foreach ($main as $key => $value) {
            if ($model->isTranslatable($key)) {
                $trans[$key] = $value;
                unset($main[$key]);
            }
        }

        $pivot = [];
        foreach ($trans as $field => $locales) {
            foreach ($locales as $locale => $value) {
                $pivot[$locale][$field] = $value;
            }
        }

        $inserts = [];
        foreach ($pivot as $locale => $fields) {
            $row['attr'] = [
                $model->getForeignKey() => $model->getKey(),
                'locale' => $locale
            ];
            foreach ($fields as $field => $value) {
                $row['val'][$field] = $value;
            }
            $inserts[] = $row;
        }

        return [$main, $inserts];
    }

    /**
     * Delete the model from the database
     *
     * @param Model $model
     * @return int
     */
    public function delete(Model $model)
    {
        if ($this->fire($model, 'deleting', true) === false) {
            return false;
        }

        $query = $this->conn->table($model->getTable());
        if ($result = $query->where($model->getKeyName(), $model->getKey())->delete()) {
            $model->setExists(false);
        }

        $this->fire($model, 'deleted');

        return $result;
    }

    /**
     * Trigger event for fields
     *
     * @param Model  $model
     * @param string $event
     * @param bool   $halt
     * @return bool
     */
    protected function fire(Model $model, $event, $halt = false)
    {
        return $model->fillEvents(new Event)->fire($event, $this->conn, $halt);
    }
}
