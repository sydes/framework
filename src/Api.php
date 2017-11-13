<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */
namespace Sydes;

class Api
{
    private $host;
    private $version;

    public function __construct($ver, $host)
    {
        $this->version = $ver;
        $this->host = $host;
    }

    public function checkUpdate()
    {
        return $this->get('update/check/'.$this->version);
    }

    public function getTranslations($module)
    {
        return $this->json('translations/'.rawurlencode($module));
    }

    public function loadTranslation($module, $locale)
    {
        return $this->get('translation/'.rawurlencode($module).'/'.rawurlencode($locale));
    }

    public function get($path)
    {
        $data = httpGet($this->host.$path.'?token='.md5($_SERVER['HTTP_HOST']));

        return !empty($data) ? $data : false;
    }

    public function json($path)
    {
        if (!$data = $this->get($path)) {
            return false;
        }

        return json_decode($data, true);
    }
}
