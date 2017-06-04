<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes;

use Psr\Container\ContainerInterface;

class App
{
    /** @var ContainerInterface */
    protected static $container;

    /**
     * @return ContainerInterface
     */
    public static function getContainer()
    {
        return static::$container;
    }

    /**
     * @param ContainerInterface $container
     */
    public static function setContainer(ContainerInterface $container)
    {
        self::$container = $container;
    }

}
