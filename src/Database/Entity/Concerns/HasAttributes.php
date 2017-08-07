<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Database\Entity\Concerns;

trait HasAttributes
{
    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * List of dirty attributes
     *
     * @var array
     */
    protected $changed = [];

    public function getAttribute($key)
    {
        if (!$key) {
            return null;
        }

        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        return null;
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $this->changed[$key] = 1;
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Determine if the model or given attribute(s) have been modified.
     *
     * @param array|string|null $attrs
     * @return bool
     */
    public function isDirty($attrs = null)
    {
        if (is_null($attrs)) {
            return count($this->changed) > 0;
        }

        foreach ((array)$attrs as $attr) {
            if (isset($this->changed[$attr])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the model or given attribute(s) have remained the same.
     *
     * @param array|string|null $attrs
     * @return bool
     */
    public function isClean($attrs = null)
    {
        return !$this->isDirty($attrs);
    }

    /**
     * Mark model as saved
     *
     * @return $this
     */
    public function clean()
    {
        $this->changed = [];

        return $this;
    }
}
