<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */
namespace Sydes\Settings;

class FileDriver implements DriverInterface
{
    public function get($path)
    {
        $ret = [];
        if (file_exists($path)) {
            $ret = include $path;
        }

        return $ret;
    }

    public function set($path, $data)
    {
        array2file($data, $path);
    }
}
