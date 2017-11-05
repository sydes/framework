<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Services;

use Psr\Http\Message\RequestInterface;
use Sydes\Contracts\Http\Request;

class DefaultServicesProvider implements ServiceProviderInterface
{
    public function register(\DI\Container $c)
    {
        $c->set('Sydes\Http\Request', function () {
            return \Sydes\Http\Request::capture();
        });
        $c->set(Request::class, \DI\get('Sydes\Http\Request'));
        $c->set(RequestInterface::class, \DI\get('Sydes\Http\Request'));
    }
}
