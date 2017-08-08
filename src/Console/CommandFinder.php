<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Console;

class CommandFinder
{
    private $app;
    private $dir;

    public function __construct($app, $container)
    {
        session_id('cli');
        session_start();

        if (!isset($_SESSION['site'])) {
            $_SESSION['site'] = 1;
        }

        $_SERVER['HTTP_HOST'] = 'from.cli';

        $container->set('site.id', $_SESSION['site']);
        $container->get('translator')->init('en');

        $app->useContainer($container);

        $this->app = $app;
        $this->dir = $container->get('dir.site.this');
    }

    public function find()
    {
        if (is_dir($this->dir)) {
            $modules = include $this->dir.'/modules.php';
        } else {
            $modules = ['Main' => []];
        }

        foreach ($modules as $name => $module) {
            $dir = moduleDir($name);
            if (file_exists($dir.'/routes/console.php')) {
                $this->load($dir.'/routes/console.php');
            }
        }
    }

    public function load($_path)
    {
        $app = $this->app;
        include $_path;
    }
}
