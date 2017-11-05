<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Http;

use Sydes\Contracts\Http\Request as RequestContract;
use Sydes\Support\Str;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\ServerRequestFactory;

class Request extends ServerRequest implements RequestContract
{
    use Concerns\InteractsWithContentTypes,
        Concerns\InteractsWithInput;

    /**
     * The decoded JSON content for the request.
     *
     * @var string
     */
    protected $json;

    /**
     * Create an request from a Diactoros instance.
     *
     * @return static
     */
    public static function capture()
    {
        $r = ServerRequestFactory::fromGlobals();

        return new static(
            $r->getServerParams(),
            $r->getUploadedFiles(),
            $r->getUri(),
            $r->getMethod(),
            $r->getBody(),
            $r->getHeaders(),
            $r->getCookieParams(),
            $r->getQueryParams(),
            $r->getParsedBody(),
            $r->getProtocolVersion()
        );
    }

    /**
     * Gets the request "intended" method.
     * The _method request parameter can be used to determine the HTTP method,
     * The method is always an uppercased string.
     *
     * @return string The request method
     */
    public function method()
    {
        $method = parent::getMethod();

        if ($method == 'POST') {
            if (!$method = $this->header('X-HTTP-METHOD-OVERRIDE')) {
                $method = $this->input('_method', 'POST');
            }
        }

        return strtoupper($method);
    }

    /**
     * Gets the "real" request method.
     *
     * @return string The request method
     */
    public function getRealMethod()
    {
        return parent::getMethod();
    }

    /**
     * @param string $method
     * @return bool
     */
    public function isMethod($method)
    {
        return $this->method() == strtoupper($method);
    }

    /**
     * Get the URL (no query string) for the request.
     *
     * @return string
     */
    public function url()
    {
        return (string) $this->getUri()->withQuery('')->withFragment('');
    }

    /**
     * Get the full URL for the request.
     *
     * @return string
     */
    public function fullUrl()
    {
        return (string) $this->getUri();
    }

    /**
     * Get the full URL for the request with the added query string parameters.
     *
     * @param  array  $query
     * @return string
     */
    public function fullUrlWithQuery(array $query)
    {
        return (string) $this->getUri()->withQuery(
            http_build_query(array_merge($this->getQueryParams(), $query))
        );
    }

    /**
     * Get the current path info for the request.
     *
     * @return string
     */
    public function path()
    {
        $pattern = trim($this->getUri()->getPath(), '/');

        return $pattern == '' ? '/' : $pattern;
    }

    /**
     * Get the current decoded path info for the request.
     *
     * @return string
     */
    public function decodedPath()
    {
        return rawurldecode($this->path());
    }

    /**
     * Determine if the current request URI matches a pattern.
     *
     * @param array $patterns
     * @return bool
     */
    public function is(...$patterns)
    {
        foreach ($patterns as $pattern) {
            if (Str::is($pattern, $this->decodedPath())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the request is the result of an AJAX call.
     *
     * @return bool
     */
    public function ajax()
    {
        return $this->getHeaderLine('X-Requested-With') == 'XMLHttpRequest';
    }

    /**
     * Determine if the request is the result of an PJAX call.
     *
     * @return bool
     */
    public function pjax()
    {
        return $this->getHeaderLine('X-PJAX') == 'true';
    }

    /**
     * @return bool
     */
    public function secure()
    {
        $https = $this->server('HTTPS');

        return !empty($https) && 'off' !== strtolower($https);
    }

    public function getIp()
    {
        return $this->server('REMOTE_ADDR');
    }

    /**
     * Get the JSON payload for the request.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function json($key = null, $default = null)
    {
        if (!isset($this->json)) {
            $this->json = json_decode($this->getBody(), true);
        }

        return data_get($this->json, $key, $default);
    }

    /**
     * Get the input source for the request.
     *
     * @return array
     */
    protected function getInputSource()
    {
        if ($this->isJson()) {
            return $this->json();
        }

        return $this->getRealMethod() == 'GET' ? $this->getQueryParams() : $this->post();
    }
}
