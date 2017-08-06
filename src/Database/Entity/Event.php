<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Database\Entity;

use Sydes\Database\Connection;
use Sydes\Database\Schema\Blueprint;

class Event
{
    private $events = [];

    public function on($key, \Closure $callback)
    {
        $this->events[$key][] = $callback;
    }

    public function fire($key, $db, $halt = false)
    {
        if (!isset($this->events[$key])) {
            return true;
        }

        foreach ($this->events[$key] as $event) {
            if ($event($db) === false && $halt) {
                return false;
            }
        }

        return true;
    }

    public function createTable(Blueprint $t, Connection $conn)
    {
        if (!isset($this->events['create'])) {
            return true;
        }

        foreach ($this->events['create'] as $event) {
            $event($t, $conn);
        }

        return true;
    }
}
