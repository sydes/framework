<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */
namespace Sydes\Exception;

class BaseHandler
{
    public function render(\Exception $e, $debugLevel)
    {
        if ($debugLevel == 0) {
            return $this->defaultResponse();
        }

        return html($this->defaultTemplate($e->getMessage(), $this->getContent($e, $debugLevel)), 500);
    }

    public function defaultResponse()
    {
        return html($this->defaultTemplate(
            '500 Internal Server Error',
            '<p>Sorry, something went wrong</p><p>Try to refresh this page later'.
            ' or feel free to contact us if the problem persists</p>'),
            500);
    }

    public function defaultTemplate($title, $content)
    {
        $title = $title ?: 'Exception without message';

        return "<!DOCTYPE html><html><head><meta charset=\"utf-8\">".
        "<title>{$title}</title><style>body{margin:0;padding:30px;".
        "font:14px/1.5 Helvetica,Arial,sans-serif;}h1{margin:0;font-size:36px;".
        "font-weight:normal;line-height:36px;}</style></head><body><h1>{$title}</h1>".
        "{$content}</body></html>";
    }

    public function getContent(\Exception $e, $debugLevel)
    {
        return $debugLevel == 1 ? '' : nl2br($e->getFile().' on '.$e->getLine()."\n\n".$e->getTraceAsString());
    }
}
