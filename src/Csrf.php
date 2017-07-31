<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */
namespace Sydes;

use Psr\Http\Message\ResponseInterface;

class Csrf
{
    protected $storage;
    protected $storageLimit;
    protected $strength;
    protected $keyPair;

    /**
     * Create new CSRF guard
     *
     * @param int $strength
     * @param int $storageLimit
     * @throws \RuntimeException if the strength too small
     */
    public function __construct(Http\Request $request, $strength = 16, $storageLimit = 200)
    {
        $this->request = $request;

        if ($strength < 16) {
            throw new \RuntimeException('Minimum strength of CSRF token is 16');
        }
        $this->strength = $strength;

        if (!array_key_exists('csrf', $_SESSION)) {
            $_SESSION['csrf'] = [];
        }
        $this->storage = &$_SESSION['csrf'];
        $this->storageLimit = $storageLimit;
        $this->keyPair = null;
    }

    public function check()
    {
        if (in_array($this->request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            $body = $this->request->getParsedBody();
            $body = $body ? (array)$body : [];
            $name = ifsetor($body['csrf_name'], false);
            $value = ifsetor($body['csrf_value'], false);
            if (!$name || !$value || !$this->validateToken($name, $value)) {
                $this->generateToken();
                abort(400, t('invalid_csrf_token'));
            }
        }

        $this->generateToken();

        // Enforce the storage limit
        while (count($this->storage) > $this->storageLimit) {
            array_shift($this->storage);
        }
    }

    public function appendHeader(ResponseInterface &$response)
    {
        if (!in_array($this->request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH']) || !$this->request->isAjax()) {
            return;
        }

        $response = $response
            ->withHeader('x-csrf-name', $this->getTokenName())
            ->withHeader('x-csrf-value', $this->getTokenValue());
    }

    /**
     * Generates a new CSRF token
     */
    public function generateToken()
    {
        $name = uniqid('csrf');
        $value = $this->createToken();
        $this->saveToStorage($name, $value);

        $this->keyPair = [
            'csrf_name' => $name,
            'csrf_value' => $value
        ];
    }

    /**
     * Validate CSRF token from current request
     * against token value stored in $_SESSION
     *
     * @param string $name  CSRF name
     * @param string $value CSRF token value
     *
     * @return bool
     */
    public function validateToken($name, $value)
    {
        $token = $this->getFromStorage($name);
        if (function_exists('hash_equals')) {
            $result = ($token !== false && hash_equals($token, $value));
        } else {
            $result = ($token !== false && $token === $value);
        }
        $this->removeFromStorage($name);

        return $result;
    }

    /**
     * @return string
     */
    public function getTokenName()
    {
        return $this->keyPair['csrf_name'];
    }

    /**
     * @return string
     */
    public function getTokenValue()
    {
        return $this->keyPair['csrf_value'];
    }

    protected function createToken()
    {
        return bin2hex(openssl_random_pseudo_bytes($this->strength));
    }

    protected function saveToStorage($name, $value)
    {
        $this->storage[$name] = $value;
    }

    protected function getFromStorage($name)
    {
        return ifsetor($this->storage[$name], false);
    }

    protected function removeFromStorage($name)
    {
        unset($this->storage[$name]);
    }
}
