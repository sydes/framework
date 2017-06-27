<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */
namespace Sydes;

abstract class Dao
{
    /** @var \PDO */
    protected $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function setDb(Database $db) {
        $this->db = $db;
    }

    public function getDb() {
        return $this->db;
    }
}
