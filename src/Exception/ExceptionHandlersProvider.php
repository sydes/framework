<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Exception;

use Sydes\Services\ServiceProviderInterface;

class ExceptionHandlersProvider implements ServiceProviderInterface
{
    public function register(\DI\Container $c)
    {
        $c->set('RedirectExceptionHandler', \DI\value(function (RedirectException $e) {
            return redirect($e->getUrl());
        }));

        $c->set('AppExceptionHandler', \DI\value(function (AppException $e) use ($c) {
            $doc = document();
            if ($c->get('section') == 'front') {
                if (model('Themes')->getActive()->getLayouts()->exists('error'.$e->getCode())) {
                    $doc->data['layout'] = 'error'.$e->getCode();
                } else {
                    $doc->data['content'] = '<h1>'.t('error_'.$e->getCode().'_text').'</h1><p>'.$e->getMessage().'</p>';
                }
            } else {
                alert($e->getMessage(), 'danger');
            }

            return html($c->get('renderer')->render($doc), $e->getCode());
        }));

        $c->set('ConfirmationExceptionHandler', \DI\value(function () {
            $doc = document([
                'content' => view('main/confirm', [
                    'message'    => t('confirm_deletion'),
                    'return_url' => $c->get('request')->getHeaderLine('Referer') ?: '/admin',
                ]),
            ]);

            return html($c->get('renderer')->render($doc), 200);
        }));

        $c->set('defaultErrorHandler', \DI\value(function (\Exception $e) use ($c) {
            $debugLevel = $c->get('settings')['debugLevel'];
            $handler = new BaseHandler;

            if ($debugLevel == 0) {
                return $handler->defaultResponse();
            }

            alert($e->getMessage().'<br>'.$handler->getContent($e, $debugLevel), 'danger');

            return html($c->get('renderer')->render(document()), 500);
        }));

        $c->set('finalExceptionHandler', function () {
            return new BaseHandler;
        });
    }
}
