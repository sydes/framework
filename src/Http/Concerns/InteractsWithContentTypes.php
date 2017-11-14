<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Http\Concerns;

use Sydes\Http\AcceptHeader;
use Sydes\Support\Str;

trait InteractsWithContentTypes
{
    /**
     * @var array
     */
    protected $acceptableContentTypes = null;

    /**
     * @var array
     */
    protected static $formats = [
        'html' => ['text/html', 'application/xhtml+xml'],
        'txt'  => ['text/plain'],
        'js'   => ['application/javascript', 'application/x-javascript', 'text/javascript'],
        'css'  => ['text/css'],
        'json' => ['application/json', 'application/x-json'],
        'xml'  => ['text/xml', 'application/xml', 'application/x-xml'],
        'rdf'  => ['application/rdf+xml'],
        'atom' => ['application/atom+xml'],
        'rss'  => ['application/rss+xml'],
        'form' => ['application/x-www-form-urlencoded'],
    ];

    /**
     * Determine if the given content types match.
     *
     * @param  string $actual
     * @param  string $type
     * @return bool
     */
    public static function matchesType($actual, $type)
    {
        if ($actual === $type) {
            return true;
        }

        $split = explode('/', $actual);

        return isset($split[1]) && preg_match('#'.preg_quote($split[0], '#').'/.+\+'.preg_quote($split[1], '#').'#',
                $type);
    }

    /**
     * Determine if the request is sending JSON.
     *
     * @return bool
     */
    public function isJson()
    {
        return Str::contains($this->header('Content-Type'), ['/json', '+json']);
    }

    /**
     * Determine if the current request probably expects a JSON response.
     *
     * @return bool
     */
    public function expectsJson()
    {
        return ($this->ajax() && !$this->pjax()) || $this->wantsJson();
    }

    /**
     * Determine if the current request is asking for JSON in return.
     *
     * @return bool
     */
    public function wantsJson()
    {
        $acceptable = $this->getAcceptableContentTypes();

        return isset($acceptable[0]) && Str::contains($acceptable[0], ['/json', '+json']);
    }

    /**
     * Determines whether the current requests accepts a given content type.
     *
     * @param  string|array $contentTypes
     * @return bool
     */
    public function accepts($contentTypes)
    {
        $accepts = $this->getAcceptableContentTypes();

        if (count($accepts) === 0) {
            return true;
        }

        $types = (array)$contentTypes;

        foreach ($accepts as $accept) {
            if ($accept === '*/*' || $accept === '*') {
                return true;
            }

            foreach ($types as $type) {
                if ($this->matchesType($accept, $type) || $accept === strtok($type, '/').'/*') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Return the most suitable content type from the given array based on content negotiation.
     *
     * @param  string|array $contentTypes
     * @return string|null
     */
    public function prefers($contentTypes)
    {
        $accepts = $this->getAcceptableContentTypes();

        $contentTypes = (array)$contentTypes;

        foreach ($accepts as $accept) {
            if (in_array($accept, ['*/*', '*'])) {
                return $contentTypes[0];
            }

            foreach ($contentTypes as $contentType) {
                $type = $contentType;

                if (!is_null($mimeType = $this->getMimeType($contentType))) {
                    $type = $mimeType;
                }

                if ($this->matchesType($type, $accept) || $accept === strtok($type, '/').'/*') {
                    return $contentType;
                }
            }
        }

        return null;
    }

    /**
     * Gets the mime type associated with the format.
     *
     * @param string $format
     * @return string
     */
    public function getMimeType($format)
    {
        return isset(static::$formats[$format]) ? static::$formats[$format][0] : null;
    }

    /**
     * Determines whether a request accepts JSON.
     *
     * @return bool
     */
    public function acceptsJson()
    {
        return $this->accepts('application/json');
    }

    /**
     * Determines whether a request accepts HTML.
     *
     * @return bool
     */
    public function acceptsHtml()
    {
        return $this->accepts('text/html');
    }

    /**
     * Get the data format expected in the response.
     *
     * @param  string $default
     * @return string
     */
    public function format($default = 'html')
    {
        foreach ($this->getAcceptableContentTypes() as $type) {
            if ($format = $this->getFormat($type)) {
                return $format;
            }
        }

        return $default;
    }

    /**
     * Gets the format associated with the mime type.
     *
     * @param string $mimeType
     * @return string|null
     */
    public function getFormat($mimeType)
    {
        $canonicalMimeType = null;
        if (false !== $pos = strpos($mimeType, ';')) {
            $canonicalMimeType = substr($mimeType, 0, $pos);
        }

        foreach (static::$formats as $format => $mimeTypes) {
            if (in_array($mimeType, (array)$mimeTypes)) {
                return $format;
            }
            if (null !== $canonicalMimeType && in_array($canonicalMimeType, (array)$mimeTypes)) {
                return $format;
            }
        }

        return null;
    }

    /**
     * Gets a list of content types acceptable by the client browser.
     *
     * @return array List of content types in preferable order
     */
    public function getAcceptableContentTypes()
    {
        if (null !== $this->acceptableContentTypes) {
            return $this->acceptableContentTypes;
        }

        return $this->acceptableContentTypes = array_keys(AcceptHeader::fromString($this->header('Accept'))->all());
    }
}
