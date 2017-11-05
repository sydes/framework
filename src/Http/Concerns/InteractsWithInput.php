<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Http\Concerns;

use Psr\Http\Message\UploadedFileInterface;
use stdClass;
use Sydes\Support\Arr;
use Sydes\Support\Str;

trait InteractsWithInput
{
    /**
     * All of the converted files for the request.
     *
     * @var array
     */
    protected $convertedFiles;

    /**
     * Retrieve a server variable from the request.
     *
     * @param  string            $key
     * @param  string|array|null $default
     * @return string|array
     */
    public function server($key = null, $default = null)
    {
        return data_get($this->getServerParams(), $key, $default);
    }

    /**
     * Retrieve a header from the request.
     *
     * @param  string            $key
     * @param  string|array|null $default
     * @return string|array
     */
    public function header($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->getHeaders();
        }

        return ($header = $this->getHeaderLine($key)) ? $header : $default;
    }

    /**
     * Get the bearer token from the request headers.
     *
     * @return string|null
     */
    public function bearerToken()
    {
        $header = $this->header('Authorization', '');

        if (Str::startsWith($header, 'Bearer ')) {
            return Str::substr($header, 7);
        }

        return null;
    }

    /**
     * Determine if the request contains a given input item key.
     *
     * @param  string|array $key
     * @return bool
     */
    public function has($key)
    {
        $keys = is_array($key) ? $key : func_get_args();

        $input = $this->all();

        foreach ($keys as $value) {
            if (!Arr::has($input, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the request contains any of the given inputs.
     *
     * @param array $keys
     * @return bool
     */
    public function hasAny(...$keys)
    {
        $input = $this->all();

        foreach ($keys as $key) {
            if (Arr::has($input, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the request contains a non-empty value for an input item.
     *
     * @param  string|array $key
     * @return bool
     */
    public function filled($key)
    {
        $keys = is_array($key) ? $key : func_get_args();

        foreach ($keys as $value) {
            if ($this->isEmptyString($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the given input key is an empty string for "has".
     *
     * @param  string $key
     * @return bool
     */
    protected function isEmptyString($key)
    {
        $value = $this->input($key);

        return !is_bool($value) && !is_array($value) && trim((string)$value) === '';
    }

    /**
     * Get the keys for all of the input and files.
     *
     * @return array
     */
    public function keys()
    {
        return array_merge(array_keys($this->input()), array_keys($this->getUploadedFiles()));
    }

    /**
     * Get all of the input and files for the request.
     *
     * @param  array|mixed $keys
     * @return array
     */
    public function all($keys = null)
    {
        $input = array_replace_recursive($this->input(), $this->allFiles());

        if (!$keys) {
            return $input;
        }

        $results = [];

        foreach (is_array($keys) ? $keys : func_get_args() as $key) {
            Arr::set($results, $key, Arr::get($input, $key));
        }

        return $results;
    }

    /**
     * Retrieve an input item from the request.
     *
     * @param  string            $key
     * @param  string|array|null $default
     * @return string|array
     */
    public function input($key = null, $default = null)
    {
        return data_get(
            $this->getInputSource() + $this->getQueryParams(), $key, $default
        );
    }

    /**
     * Get a subset containing the provided keys with values from the input data.
     *
     * @param  array|mixed $keys
     * @return array
     */
    public function only($keys)
    {
        $results = [];

        $input = $this->all();

        $placeholder = new stdClass;

        foreach (is_array($keys) ? $keys : func_get_args() as $key) {
            $value = data_get($input, $key, $placeholder);

            if ($value !== $placeholder) {
                Arr::set($results, $key, $value);
            }
        }

        return $results;
    }

    /**
     * Get all of the input except for a specified array of items.
     *
     * @param  array|mixed $keys
     * @return array
     */
    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        $results = $this->all();

        Arr::forget($results, $keys);

        return $results;
    }

    /**
     * Retrieve a query string item from the request.
     *
     * @param  string            $key
     * @param  string|array|null $default
     * @return string|array
     */
    public function query($key = null, $default = null)
    {
        return data_get($this->getQueryParams(), $key, $default);
    }

    /**
     * Retrieve a request payload item from the request.
     *
     * @param  string            $key
     * @param  string|array|null $default
     *
     * @return string|array
     */
    public function post($key = null, $default = null)
    {
        if (!$post = $this->getParsedBody()) {
            $post = [];
        }

        return data_get($post, $key, $default);
    }

    /**
     * Determine if a cookie is set on the request.
     *
     * @param  string $key
     * @return bool
     */
    public function hasCookie($key)
    {
        return !is_null($this->cookie($key));
    }

    /**
     * Retrieve a cookie from the request.
     *
     * @param  string            $key
     * @param  string|array|null $default
     * @return string|array
     */
    public function cookie($key = null, $default = null)
    {
        return data_get($this->getCookieParams(), $key, $default);
    }

    /**
     * Get an array of all of the files on the request.
     *
     * @return array
     */
    public function allFiles()
    {
        return $this->getUploadedFiles();
    }

    /**
     * Determine if the uploaded data contains a file.
     *
     * @param  string $key
     * @return bool
     */
    public function hasFile($key)
    {
        if (!is_array($files = $this->file($key))) {
            $files = [$files];
        }

        foreach ($files as $file) {
            if ($this->isValidFile($file)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check that the given file is a valid file instance.
     *
     * @param  mixed $file
     * @return bool
     */
    protected function isValidFile($file)
    {
        return $file instanceof UploadedFileInterface && $file->getClientFilename() !== '';
    }

    /**
     * Retrieve a file from the request.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return array|null
     */
    public function file($key = null, $default = null)
    {
        return data_get($this->allFiles(), $key, $default);
    }
}