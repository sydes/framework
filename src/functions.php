<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

use Sydes\App;
use Sydes\Html\Base;
use Sydes\Http\Redirect;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

include 'Support/helpers.php';

/**
 * Print formatted array.
 *
 * @param array|object $array
 * @param bool  $return
 *
 * @return string|bool
 */
function pre($array, $return = false)
{
    $pre = '<pre>'.print_r($array, true).'</pre>';
    if ($return) {
        return $pre;
    }
    echo $pre;
    return true;
}

/**
 * Load and execute file with given data.
 *
 * @param string $file
 * @param array  $data
 * @return string
 */
function render($file, $data = [])
{
    return (new Sydes\View\Engines\PhpEngine)->get($file, $data);
}

/**
 * Generate random string.
 *
 * @param int $length
 * @return null|string
 */
function token($length)
{
    $chars = ['A','B','C','D','E','F','G','H','J','K','L','M','N','O','P','Q','R',
        'S','T','U','V','W','X','Y','Z','a','b','c','d','e','f','g','h',
        'i','j','k','m','n','o','p','q','r','s','t','u','v','w','x','y',
        'z','1','2','3','4','5','6','7','8','9',];
    if ($length < 0 || $length > 58) {
        $length = 16;
    }
    shuffle($chars);
    return implode('', array_slice($chars, 0, $length));
}

/**
 * Get the available container instance.
 *
 * @param string $id
 * @return mixed|\Psr\Container\ContainerInterface
 */
function app($id = null)
{
    if (is_null($id)) {
        return App::getContainer();
    }

    return App::getContainer()->get($id);
}

/**
 * Translate string and insert contextual data.
 *
 * @param string $text
 * @param array  $context
 * @return string
 */
function t($text, array $context = [])
{
    return app('translator')->translate($text, $context);
}

/**
 * Pluralize and translate string.
 *
 * @param string $text
 * @param int    $count
 * @param array  $context
 * @return string
 */
function p($text, $count, array $context = [])
{
    return app('translator')->pluralize($text, $count, $context);
}

/**
 * Translated to current locale function date()
 *
 * @param string $format
 * @param int    $timestamp
 * @return string
 */
function d($format, $timestamp = null)
{
    return app('translator')->date($format, $timestamp);
}

/**
 * @param string $text
 * @param array  $context
 * @return string
 */
function interpolate($text, array $context = [])
{
    if (false === strpos($text, '{') || empty($context)) {
        return $text;
    }

    $replace = [];
    foreach ($context as $key => $val) {
        if (is_null($val) || is_scalar($val) || (is_object($val) && method_exists($val, "__toString"))) {
            $replace['{'.$key.'}'] = $val;
        } elseif (is_object($val)) {
            $replace['{'.$key.'}'] = '[object '.get_class($val).']';
        } else {
            $replace['{'.$key.'}'] = '['.gettype($val).']';
        }
    }

    return strtr($text, $replace);
}

/**
 * Print array to file for include.
 *
 * @param array  $array
 * @param string $filename
 */
function array2file($array, $filename)
{
    $string = '<?php return '.var_export($array, true).';';
    file_put_contents($filename, $string, LOCK_EX);
    chmod($filename, 0777);
}

/**
 * Sends GET request like HTTP client
 * @param string $url
 * @return mixed|null|string
 */
function httpGet($url)
{
    $data = null;
    $timeout = 30;
    $userAgent = 'SyDES '.SYDES_VERSION;

    if (ini_get('allow_url_fopen')) {
        $ctx = stream_context_create([
            'http' => [
                'timeout' => $timeout,
                'user_agent' => $userAgent,
            ],
        ]);
        $data = file_get_contents($url, false, $ctx);
    } elseif (function_exists('curl_init')) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);
    }

    return $data;
}

/**
 * @param $destination string Folder to unpack
 * @param $archive     string Url of archive
 * @return bool
 */
function extractOuterZip($destination, $archive)
{
    $result = false;
    $temp = app('dir.temp').'/'.token(6);
    file_put_contents($temp, httpGet($archive));

    $zip = new ZipArchive;
    if ($zip->open($temp) === true) {
        $zip->extractTo($destination);
        $zip->close();

        $result = true;
    }

    unlink($temp);
    return $result;
}

/**
 * Get the document instance.
 *
 * @param array $data
 * @return Sydes\Document
 */
function document($data = [])
{
    return new Sydes\Document($data);
}

/**
 * Get basic PSR-7 Response object
 *
 * @param string $content
 * @param int    $status
 * @param array  $headers
 * @return ResponseInterface
 */
function response($content = '', $status = 200, array $headers = [])
{
    $res = new Response('php://memory', $status, $headers);
    $res->getBody()->write($content);

    return $res;
}

/**
 * Get PSR-7 Response object with Content-Type as plain text
 *
 * @param string $text
 * @param int    $status
 * @param array  $headers
 * @return ResponseInterface
 */
function text($text, $status = 200, $headers = [])
{
    return new Response\TextResponse($text, $status, $headers);
}

/**
 * Get PSR-7 Response object with Content-Type as html
 *
 * @param string $html
 * @param int    $status
 * @param array  $headers
 * @return ResponseInterface
 */
function html($html, $status = 200, $headers = [])
{
    return new Response\HtmlResponse($html, $status, $headers);
}

/**
 * Get PSR-7 Response object without body
 *
 * @param int    $status
 * @param array  $headers
 * @return ResponseInterface
 */
function head($status = 204, $headers = [])
{
    return new Response\EmptyResponse($status, $headers);
}

/**
 * Get PSR-7 Response object with Content-Type as json
 *
 * @param array $array
 * @param int   $status
 * @param array $headers
 * @param int   $encodingOptions
 * @return ResponseInterface
 */
function json($array, $status = 200, $headers = [], $encodingOptions = 79)
{
    return new Response\JsonResponse($array, $status, $headers, $encodingOptions);
}

/**
 * Get PSR-7 Response object with redirect
 *
 * @param string $uri
 * @param int    $status
 * @param array  $headers
 * @return Redirect
 */
function redirect($uri = '/', $status = 302, $headers = [])
{
    return new Redirect($uri, $status, $headers);
}

/**
 * Create a new redirect response to the previous location.
 *
 * @return Redirect
 */
function back()
{
    $to = app('request')->getHeaderLine('Referer') ?: '/';
    return redirect($to);
}

/**
 * Create a new response with downloadable file
 *
 * @param string $file
 * @param string $name
 * @param int    $status
 * @param array  $headers
 * @return Sydes\Http\AttachmentResponse
 */
function download($file, $name = null, $status = 200, $headers = [])
{
    return new Sydes\Http\AttachmentResponse($file, $name, $status, $headers);
}

/**
 * Create a new response with downloadable content
 *
 * @param string $content
 * @param string $name
 * @param int    $status
 * @param array  $headers
 * @return Response
 */
function downloadContent($content, $name, $status = 200, $headers = [])
{
    $headers = array_replace($headers, [
        'content-length'      => strlen($content),
        'content-disposition' => sprintf('attachment; filename=%s', $name),
    ]);
    $response = new Response('php://temp', $status, $headers);
    $response->getBody()->write($content);
    if (!$response->hasHeader('Content-Type')) {
        $response = $response->withHeader('Content-Type', 'application/octet-stream');
    }
    return $response;
}

/**
 * Sets a notify message.
 *
 * @param string $message
 * @param string $status Any of bootstrap alert statuses
 * @return array
 */
function notify($message, $status = 'success')
{
    $_SESSION['notify'] = [
        'message' => $message,
        'status'  => $status,
    ];
    return ['notify' => $_SESSION['notify']];
}

/**
 * Adds a alert message.
 *
 * @param string $message
 * @param string $status Any of bootstrap alert statuses
 * @return array
 */
function alert($message, $status = 'success')
{
    $_SESSION['alerts'][] = [
        'message' => $message,
        'status'  => $status,
    ];
    return ['alerts' => $_SESSION['alerts']];
}

/**
 * Creates or loads config for extension
 *
 * @param string $extension
 * @return Sydes\Settings\Container
 */
function settings($extension)
{
    return new Sydes\Settings\Container($extension, new Sydes\Settings\SQLDriver(app('db')));
}

if (!function_exists('ifsetor')) {
    function ifsetor(&$value, $default = null)
    {
        return isset($value) ? $value : $default;
    }
}

/**
 * Escape HTML entities in a string.
 *
 * @param string $str
 * @return string
 */
function e($str)
{
    return Base::encode($str);
}

/**
 * Loads view for some module
 *
 * @param string $view module-name/view-name
 * @param array  $data
 * @return Sydes\View\View
 */
function view($view, $data = [])
{
    return app('view')->make($view, $data);
}

/**
 * @param string $file
 * @return mixed
 */
function parse_json_file($file)
{
    return json_decode(file_get_contents($file), true);
}

/**
 * @param string $file
 * @param array  $array
 * @return int
 */
function write_json_file($file, array $array)
{
    return file_put_contents($file, json_encode($array, JSON_PRETTY_PRINT));
}

/**
 * @param string $file
 * @param array  $array
 * @param bool   $process_sections
 * @return int
 */
function write_ini_file($file, array $array, $process_sections = false)
{
    $content = '';
    if ($process_sections) {
        foreach ($array as $key => $elem) {
            $content .= "[{$key}]\n";
            foreach ($elem as $key2 => $elem2) {
                if (is_array($elem2)) {
                    foreach ($elem2 as $key3 => $elem3) {
                        $content .= "{$key2}[{$key3}] = {$elem3}\n";
                    }
                } else {
                    $content .= "{$key2} = {$elem2}\n";
                }
            }
        }
    } else {
        foreach ($array as $key => $elem) {
            if (is_array($elem)) {
                foreach ($elem as $key2 => $elem2) {
                    $content .= "{$key}[{$key2}] = {$elem2}\n";
                }
            } else {
                $content .= "{$key} = {$elem}\n";
            }
        }
    }
    return file_put_contents($file, $content);
}

/**
 * Remove directory with all content
 *
 * @param string $dir Path to target folder
 * @return bool|null
 */
function removeDir($dir)
{
    if (!is_dir($dir)) return null;

    $d = opendir($dir);
    while (($entry = readdir($d)) !== false) {
        if ($entry != "." && $entry != "..") {
            is_dir($dir."/".$entry) ? removeDir($dir."/".$entry) : unlink($dir."/".$entry);
        }
    }
    closedir($d);

    return rmdir($dir);
}

function csrf_field()
{
    return '<input type="hidden" name="csrf_name" value="'.app('csrf')->getTokenName().'">
        <input type="hidden" name="csrf_value" value="'.app('csrf')->getTokenValue().'">';
}

/**
 * Generate a form field to spoof the HTTP verb used by forms.
 *
 * @param string $method
 * @return string
 */
function method_field($method)
{
    return '<input type="hidden" name="_method" value="'.$method.'">';
}

function sortByWeight($a, $b)
{
    return isset($a['weight'], $b['weight']) ? $a['weight'] - $b['weight'] : 0;
}

function tolower($string)
{
    return mb_strtolower($string, 'UTF-8');
}

/**
 * Removes an item from the array and returns its value.
 * @param array  $arr
 * @param string $key
 * @param mixed $default
 * @return mixed value
 */
function array_remove(array &$arr, $key, $default = null)
{
    if (!array_key_exists($key, $arr)) {
        return $default;
    }

    $val = $arr[$key];
    unset($arr[$key]);

    return $val;
}
