<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */
namespace Sydes\Settings;

use Sydes\Db;

class SQLDriver implements DriverInterface
{
    /** @var Db */
    private $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function get($ext)
    {
        $ret = [];
        $data = $this->db->table('settings')->where('extension', $ext)->get();
        foreach ($data as $d) {
            $ret[$d['key']] = json_decode($d['value'], true);
        }

        return $ret;
    }

    public function set($ext, $data)
    {
        $this->db->table('settings')->where('extension', $ext)->delete();

        $insert = [];
        foreach ($data as $key => $value) {
            $insert[] = [
                'extension' => $ext,
                'key' => $key,
                'value' => json_encode($value)
            ];
        }

        $this->db->table('settings')->insert($insert);
    }
}
