<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */
namespace Sydes\Router;

class Route extends \FastRoute\RouteCollector
{
    /**
     * This single route declaration creates multiple routes to handle a variety of actions on the resource
     *
     * @param string $alias
     * @param string $module
     * @param string $itemRegex
     */
    public function resource($alias, $module, $itemRegex = '\d+')
    {
        $this->get('/admin/'.$alias, $module.'@index');
        $this->get('/admin/'.$alias.'/create', $module.'@create');
        $this->post('/admin/'.$alias, $module.'@store');
        $this->get('/admin/'.$alias.'/{id:'.$itemRegex.'}', $module.'@edit');
        $this->put('/admin/'.$alias.'/{id:'.$itemRegex.'}', $module.'@update');
        $this->delete('/admin/'.$alias.'/{id:'.$itemRegex.'}', $module.'@destroy');
    }

    /**
     * This single route declaration for viewing and editing module settings
     *
     * @param string $alias
     * @param string $module
     */
    public function settings($alias, $module)
    {
        $this->get('/admin/'.$alias.'/settings', $module.'@settings');
        $this->put('/admin/'.$alias.'/settings', $module.'@updateSettings');
    }
}