<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Database\Entity\Concerns;

use Sydes\Database\Entity\Field;
use Sydes\Support\Str;

trait HasFields
{
    /**
     * List of fields with settings
     *
     * @var array
     */
    protected $fields = [];

    /**
     * List of field types
     *
     * @var array
     */
    protected static $fieldTypes;

    protected $bootedFields = [];

    protected $booted = false;

    /**
     * Returns field list config
     *
     * @return array
     */
    public function getFieldList()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @param string $key
     * @param array  $field with type, settings, weight and other
     * @return $this
     */
    public function addField($key, array $field)
    {
        $this->fields[$key] = $field;

        return $this;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasField($key)
    {
        return isset($this->fields[$key]);
    }

    /**
     * Boot all fields and return them
     *
     * @param array $keys
     * @return Field[]
     */
    public function getFields(array $keys = [])
    {
        if (!$this->booted) {
            $this->booted = true;

            foreach ($this->fields as $name => $void) {
                $this->field($name);
            }

            // sort fields by weight even if some was booted previously
            uasort($this->fields, 'sortByWeight');
            $this->bootedFields = array_replace($this->fields, $this->bootedFields);
        }

        if (empty($keys)) {
            return $this->bootedFields;
        }

        $keys = array_flip($keys);

        return array_intersect_key(array_replace($keys, $this->bootedFields), $keys);
    }

    /**
     * Init field and return him
     *
     * @param string $name
     * @return Field
     */
    public function field($name)
    {
        if (!isset($this->bootedFields[$name])) {
            $this->bootedFields[$name] = $this->bootField($name);
        }

        return $this->bootedFields[$name];
    }

    /**
     * @param string $name
     * @return Field
     */
    public function bootField($name)
    {
        if (!isset($this->fields[$name])) {
            throw new \InvalidArgumentException('Field '.$name.' not found in '.get_class($this));
        }

        $field = $this->fields[$name];

        if (!isset($field['settings'])) {
            $field['settings'] = [];
        }

        $class = self::$fieldTypes[$field['type']];

        $value = null;
        if ($this->isRelational($name)) {
            if ($rel = $this->getRelationValue($name)) {
                $value = $rel->getResults();
            }
        } else {
            $value = $this->getAttribute($name);
        }

        $field = new $class($name, $value, $field['settings']);

        if (isset($rel)) {
            $field->setRelation($rel);
        }

        return $field;
    }

    /**
     * @param string $field
     */
    public function isRelational($field)
    {
        return Str::contains($this->fields[$field]['type'], 'Relation');
    }

    /**
     * @return array
     */
    public function getRelationalFields()
    {
        $attrs = [];
        foreach ($this->fields as $key => $field) {
            if (Str::contains($field['type'], 'Relation')) {
                $attrs[] = $key;
            }
        }

        return $attrs;
    }

    /**
     * Check that field has translations
     *
     * @param string $key
     * @return bool
     */
    public function isTranslatable($key)
    {
        return isset($this->fields[$key]['settings']['translatable']);
    }

    /**
     * @return bool
     */
    public function hasTranslatable()
    {
        foreach ($this->fields as $key => $field) {
            if (isset($field['settings']['translatable'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getTranslatableFields()
    {
        $attrs = [];

        foreach ($this->fields as $key => $field) {
            if (isset($field['settings']['translatable'])) {
                $attrs[] = $key;
            }
        }

        return $attrs;
    }

    /**
     * For translatable fields
     *
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        foreach ($this->getTranslatableFields() as $field) {
            $this->field($field)->setLocale($locale);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getFieldTypes()
    {
        return self::$fieldTypes;
    }

    /**
     * @param array $fieldTypes
     */
    public static function setFieldTypes($fieldTypes)
    {
        self::$fieldTypes = $fieldTypes;
    }
}
