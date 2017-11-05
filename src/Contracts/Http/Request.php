<?php

namespace Sydes\Contracts\Http;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

interface Request extends ServerRequestInterface
{
    /**
     * @return string
     */
    public function method();

    /**
     * @return string
     */
    public function getRealMethod();

    /**
     * @param string $method
     * @return bool
     */
    public function isMethod($method);

    /**
     * @return string
     */
    public function url();

    /**
     * @return string
     */
    public function fullUrl();

    /**
     * @param array $query
     * @return string
     */
    public function fullUrlWithQuery(array $query);

    /**
     * @return string
     */
    public function path();

    /**
     * @return string
     */
    public function decodedPath();

    /**
     * @param array ...$patterns
     * @return bool
     */
    public function is(...$patterns);

    /**
     * @param  string            $key
     * @param  string|array|null $default
     * @return string|array
     */
    public function server($key = null, $default = null);

    /**
     * @param string            $key
     * @param string|array|null $default
     * @return mixed
     */
    public function header($key = null, $default = null);

    /**
     * @return string|null
     */
    public function bearerToken();

    /**
     * @param string $key
     * @return bool
     */
    public function has($key);

    /**
     * @param array ...$keys
     * @return bool
     */
    public function hasAny(...$keys);

    /**
     * @param string|array $key
     * @return bool
     */
    public function filled($key);

    /**
     * @return array
     */
    public function keys();

    /**
     * @param  array|mixed $keys
     * @return array
     */
    public function all($keys = null);

    /**
     * @param  string            $key
     * @param  string|array|null $default
     * @return string|array
     */
    public function input($key = null, $default = null);

    /**
     * @param  array|mixed $keys
     * @return array
     */
    public function only($keys);

    /**
     * @param  array|mixed $keys
     * @return array
     */
    public function except($keys);

    /**
     * @param  string            $key
     * @param  string|array|null $default
     * @return string|array
     */
    public function query($key = null, $default = null);

    /**
     * @param  string            $key
     * @param  string|array|null $default
     * @return string|array
     */
    public function post($key = null, $default = null);

    /**
     * @param  string            $key
     * @param  string|array|null $default
     * @return string|array
     */
    public function cookie($key = null, $default = null);

    /**
     * @param  string $key
     * @return bool
     */
    public function hasCookie($key);

    /**
     * @param string $key
     * @param mixed  $default
     * @return UploadedFileInterface|array
     */
    public function file($key = null, $default = null);

    /**
     * @param string $key
     * @return bool
     */
    public function hasFile($key);
}
