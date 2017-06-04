<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Services;

class ServiceLoader
{
    protected $container;

    public function __construct(\DI\Container $container)
    {
        $this->container = $container;
    }

    /**
     * Registers a service provider.
     *
     * @param ServiceProviderInterface $provider
     * @param array                    $values An array of values that customizes the provider
     *
     * @return static
     */
    public function register(ServiceProviderInterface $provider, array $values = [])
    {
        $provider->register($this->container);

        foreach ($values as $key => $value) {
            $this->container->set($key, $value);
        }

        return $this;
    }
}
