<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */
namespace Sydes\Exception;

class RedirectException extends \Exception
{
    protected $url;

    public function __construct($url)
    {
        $this->url = $url;
        parent::__construct();
    }

    public function getUrl()
    {
        return $this->url;
    }
}
