<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Services;

use Zend\Diactoros\ServerRequestFactory;

class DefaultServicesProvider implements ServiceProviderInterface
{
    public function register(\DI\Container $c)
    {
        $c->set('Sydes\Http\Request', function () {
            $r = ServerRequestFactory::fromGlobals();

            return new \Sydes\Http\Request(
                $r->getServerParams(),
                $r->getUploadedFiles(),
                $r->getUri(),
                $r->getMethod(),
                $r->getBody(),
                $r->getHeaders(),
                $r->getCookieParams(),
                $r->getQueryParams(),
                $r->getParsedBody(),
                $r->getProtocolVersion()
            );
        });
    }
}
