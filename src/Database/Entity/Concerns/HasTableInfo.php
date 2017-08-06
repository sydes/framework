<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Database\Entity\Concerns;

use Sydes\Support\Str;

trait HasTableInfo
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey;

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        if (isset($this->table)) {
            return $this->table;
        }

        return str_replace('\\', '', Str::snake(Str::plural(class_basename($this))));
    }

    /**
     * @param string $table
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        if (!$this->primaryKey) {
            if ($key = $this->getPrimaryFromFields()) {
                $this->primaryKey = $key;
            } else {
                $this->primaryKey = 'id';
            }
        }

        return $this->primaryKey;
    }

    protected function getPrimaryFromFields()
    {
        foreach ($this->fields as $key => $field) {
            if (isset($field['primary'])) {
                return $key;
            }
        }

        return false;
    }

    /**
     * Get the table qualified key name.
     *
     * @return string
     */
    public function getQualifiedKeyName()
    {
        return $this->getTable().'.'.$this->getKeyName();
    }

    /**
     * Get the default foreign key name for the model.
     *
     * @return string
     */
    public function getForeignKey()
    {
        return $this->getTableSingular().'_'.$this->getKeyName();
    }

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->getAttribute($this->getKeyName());
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function hasIncrementing()
    {
        return $this->getKeyName() == 'id';
    }

    /**
     * Get the table name where translations stored.
     *
     * @return string
     */
    public function getTranslationTable()
    {
        return $this->getTableSingular().'_translated';
    }

    /**
     * Get entity name even if class extending not used
     *
     * @return string
     */
    public function getTableSingular()
    {
        if (($table = Str::snake(class_basename($this))) == 'model' && $this->table) {
            $table = Str::singular($this->table);
        }

        return $table;
    }
}
