<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Services;

use Sydes\Settings\Container as Settings;
use Sydes\Settings\FileDriver;
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

        $c->set('renderer', function () use ($c) {
            $class = 'Sydes\Renderer\\'.ucfirst($c->get('section'));

            return new $class;
        });

        $c->set('app', function () use ($c) {
            $path = $c->get('dir.storage').'/app.php';

            return new Settings($path, new FileDriver());
        });

        $c->set('site', function () use ($c) {
            $path = $c->get('dir.site').'/'.$c->get('site.id').'/config.php';

            return new Settings($path, new FileDriver());
        });

    }
}
