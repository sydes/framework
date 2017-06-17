<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */
namespace Sydes;

class Auth
{
    /** @var User */
    protected $user;
    protected $users;

    public function __construct($userProvider, Http\Request $request)
    {
        $this->users = $userProvider;
        $this->request = $request;
    }

    /**
     * @param string $name
     * @param string $pass
     * @param bool   $remember
     * @return bool
     */
    public function attempt($name, $pass, $remember = false)
    {
        /** @var $user User */
        if (!$user = $this->users->getByName($name)) {
            return false;
        }

        if (!$user->checkPassword($pass)) {
            return false;
        }

        return $this->login($user, $remember);
    }

    /**
     * @param User $user
     * @param bool $remember
     * @return bool
     */
    public function login(User $user, $remember = false)
    {
        $this->user = $user;

        $_SESSION['hash'] = $user->get('id').':'.$this->hash($user);
        setcookie('entered', '1', time() + 604800, '/');

        if ($remember && $user->get('autoLogin') == 1) {
            setcookie('hash', $_SESSION['hash'], time() + 604800, '/');
        }

        return true;
    }

    public function logout()
    {
        session_destroy();
        setcookie('hash', '', 1, '/');
        setcookie('entered', '', 1, '/');
    }

    /**
     * @return bool
     */
    public function check()
    {
        if ($this->user === null) {
            $this->tryLogin();
        }

        return !empty($this->user);
    }

    private function tryLogin()
    {
        if (isset($_SESSION['hash'])) { // already logged in
            list($id, $hash) = explode(':', $_SESSION['hash']);

            if ($user = $this->users->get($id)) {
                if ($hash == $this->hash($user)) {
                    $this->user = $user;
                    return;
                }
            }

            $this->logout();
        } elseif (isset($_COOKIE['hash'])) { // login by cookies
            list($id, $hash) = explode(':', $_COOKIE['hash']);

            if ($user = $this->users->get($id)) {
                if ($user->get('autoLogin') == 1 && $hash == $this->hash($user)) {
                    $this->login($user, true);
                    return;
                }
            }

            $this->logout();
        }
    }

    /**
     * @param string $key
     * @return false|string|User
     */
    public function getUser($key = null)
    {
        if (empty($this->user)) {
            return false;
        }

        return $key === null ? $this->user : $this->user->get($key);
    }

    protected function hash(User $user)
    {
        return md5($user->get('username').$user->get('password').$this->request->getIp());
    }
}
