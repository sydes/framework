<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */
namespace Sydes\Http;

use Zend\Diactoros\Response\RedirectResponse;

class Redirect extends RedirectResponse
{
    private $uri;

    public function __construct($uri, $status = 302, array $headers = [])
    {
        $this->uri = (string) $uri;

        parent::__construct($uri, $status, $headers);
    }

    public function getUri()
    {
        return $this->uri;
    }
}
