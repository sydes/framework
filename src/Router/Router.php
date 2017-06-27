<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */
namespace Sydes\Router;

class Router
{
    /**
     * Path to fast route cache file.
     */
    protected $cacheFile = false;

    /** @var \FastRoute\Dispatcher */
    protected $dispatcher;

    /**
     * @param string $file
     */
    public function setCacheFile($file)
    {
        $this->cacheFile = $file;
    }

    /**
     * Dispatch router for request
     *
     * @param array $files
     * @param string $method
     * @param string $uri
     * @return array
     * @link   https://github.com/nikic/FastRoute/blob/master/src/Dispatcher.php
     */
    public function dispatch(array $files, $method, $uri)
    {
        $callback = function (Route $r) use ($files) {
            foreach ($files as $file) {
                if (file_exists($file)) {
                    include $file;
                }
            }
        };

        return $this->createDispatcher($callback)->dispatch($method, $uri);
    }

    /**
     * @return \FastRoute\Dispatcher
     */
    protected function createDispatcher($callback)
    {
        $this->dispatcher = \FastRoute\cachedDispatcher($callback, [
            'cacheFile'     => $this->cacheFile,
            'cacheDisabled' => is_bool($this->cacheFile),
            'routeCollector' => 'Sydes\Router\Route',
        ]);

        return $this->dispatcher;
    }
}
