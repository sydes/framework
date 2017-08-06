<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Database\Entity;

use Sydes\Database\Connection;
use Sydes\Database\Schema\Blueprint;

abstract class Field
{
    protected $name;
    protected $value;
    protected $settings = [];
    protected $contains = 'text';
    protected $formatters = [
        'default' => 'default_formatter',
    ];
    protected $searchable = false;
    protected $filterable = true;
    protected $sortable = true;

    /**
     * Field constructor with data and settings
     *
     * @param string $name
     * @param string $value
     * @param array  $settings
     */
    public function __construct($name, $value, array $settings = [])
    {
        $this->name = $name;
        $this->settings = array_merge([
            'required' => false,
            'helpText' => '',
            'multiple' => false,
            'default' => null,
            'label' => '',
            'formatter' => 'default',
        ], $this->settings, $settings);
        $this->fromString($value);
    }

    /**
     * Sets value from database. Can unserialize to array
     *
     * @param mixed $value
     * @return $this
     */
    public function fromString($value)
    {
        if (is_null($value)) {
            if (!is_null($this->settings['default'])) {
                $this->value = $this->settings['default'];
            }
        } else {
            if ($this->contains == 'array' && is_string($value)) {
                $value = empty($value) ? [] : json_decode($value, true);
            }

            $this->value = $value;
        }

        return $this;
    }

    /**
     * Gets string formatted value for database
     *
     * @return string
     */
    public function toString()
    {
        if ($this->contains == 'array') {
            return json_encode($this->value, JSON_UNESCAPED_UNICODE);
        }

        return $this->value;
    }

    /**
     * Sets value as is
     *
     * @param mixed $value
     * @return $this
     */
    public function set($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Gets value as is or his part
     *
     * @param string $key
     * @return mixed
     */
    public function value($key = null)
    {
        if (is_null($key)) {
            return $this->value;
        } elseif (is_array($this->value) && isset($this->value[$key])) {
            return $this->value[$key];
        }

        return null;
    }

    /**
     * Gets name of field
     *
     * @return mixed
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Gets translated label of field
     *
     * @return string
     */
    public function label()
    {
        return t($this->settings['label']);
    }

    /**
     * Gets all settings for field or one if key is provided
     *
     * @param string|null $key
     * @return array|mixed
     */
    public function settings($key = null)
    {
        return !is_null($key) ? $this->settings[$key] : $this->settings;
    }

    /**
     * @param string|array $key
     * @param mixed        $value
     * @return $this
     */
    public function setSettings($key, $value = null)
    {
        if (is_array($key)) {
            $this->settings = $key;
        } else {
            $this->settings[$key] = $value;
        }

        return $this;
    }

    /**
     * Gets list of available value formatters for renderer
     *
     * @return array
     */
    public function getFormatters()
    {
        return $this->formatters;
    }

    /**
     * Return false to cancel saving
     *
     * @return bool
     */
    public function validate()
    {
        return true;
    }

    /**
     * Defines how the field will actually display its contents on front or listing
     *
     * If $formatter provided, it will be used for render else default or one from settings
     *
     * @param \Closure|string $formatter
     * @param mixed $value
     * @return string
     */
    public function output($formatter = null, $value = null)
    {
        if ($formatter instanceof \Closure) {
            return $formatter($this);
        }

        if ($formatter === null) {
            $formatter = $this->settings['formatter'];
        }

        $formatters = $this->formatters + ['table' => 1, 'filter' => 1];
        if (!is_string($formatter) || !isset($formatters[$formatter])) {
            throw new \RuntimeException('Field formatter for "'.$this->name.'" not found');
        }

        return $this->{$formatter.'Output'}($value);
    }

    protected function defaultOutput()
    {
        return $this->value;
    }

    protected function filterOutput($value)
    {
        return \H::formGroup(
            $this->label(),
            \H::textInput('filter['.$this->name.']', $value)
        );
    }

    protected function tableOutput()
    {
        return $this->value;
    }

    /**
     * Gets form input with wrapper
     *
     * @param callable $wrapper
     * @return string
     */
    public function input($wrapper = null)
    {
        if (is_null($wrapper)) {
            $wrapper = function (Field $field) {
                return \H::formGroup(
                    $field->label(),
                    $field->defaultInput(),
                    t($field->settings('helpText'))
                );
            };
        }

        return $wrapper($this);
    }

    /**
     * Gets only input
     *
     * @return string
     */
    public function defaultInput()
    {
        return \H::textInput($this->name, $this->value, [
            'required' => $this->settings['required'],
        ]);
    }

    /**
     * Gets form with settings for this field
     *
     * @return string
     */
    public function formSettings()
    {
        return '';
    }

    /**
     * Boots event listeners in field
     *
     * @param Event $events
     */
    public function getEventListeners(Event $events)
    {
        $events->on('create', function (Blueprint $t, Connection $db) {
            $t->string($this->name);
        });
    }

    /**
     * @return bool
     */
    public function isSearchable()
    {
        return $this->searchable;
    }

    /**
     * @return bool
     */
    public function isFilterable()
    {
        return $this->filterable;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return $this->sortable;
    }
}
