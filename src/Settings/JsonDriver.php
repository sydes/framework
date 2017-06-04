<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */
namespace Sydes\Settings;

class JsonDriver implements DriverInterface
{
    public function get($path)
    {
        $ret = [];
        if (file_exists($path)) {
            $ret = parse_json_file($path);
        }

        return $ret;
    }

    public function set($path, $data)
    {
        write_json_file($path, $data);
    }
}
