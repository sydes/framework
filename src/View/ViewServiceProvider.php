<?php

namespace Sydes\View;

use Sydes\Services\ServiceProviderInterface;

class ViewServiceProvider implements ServiceProviderInterface
{
    public function register(\DI\Container $c)
    {
        $this->registerFactory($c);
        $this->registerEngineResolver($c);
    }

    public function registerFactory(\DI\Container $c)
    {
        $c->set('view', \DI\object(Factory::class)->constructor(\DI\get('view.engine.resolver'), \DI\get('event')));
    }

    public function registerEngineResolver(\DI\Container $c)
    {
        $c->set('view.engine.resolver', function () {
            $resolver = new Engines\EngineResolver;

            $resolver->register('file', function () {
                return new Engines\FileEngine;
            });

            $resolver->register('php', function () {
                return new Engines\PhpEngine;
            });

            return $resolver;
        });
    }
}
