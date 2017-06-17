<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */
namespace Sydes;

class Database
{
    /** @var PDO[] */
    protected $connections = [];
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function connection($name = null)
    {
        if (!$name) {
            $name = $this->config['default'];
        }

        if (!isset($this->connections[$name])) {
            if (!isset($this->config['connections'][$name])) {
                throw new \RuntimeException('Wrong connection name '.$name);
            }

            $current = $this->config['connections'][$name];

            $dsn = $user = $pass = null;
            if ($current['driver'] == 'sqlite') {
                $dsn = 'sqlite:'.str_replace('{dir.site.this}', app('dir.site.this'), $current['database']);
            } elseif (in_array($current['driver'], ['mysql', 'pgsql'])) {
                $dsn = $current['driver'].':host='.$current['host'].
                    ';dbname='.$current['database'].';charset='.$current['charset'];
                $user = $current['username'];
                $pass = $current['password'];
            }

            $opt = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            $this->connections[$name] = new PDO($dsn, $user, $pass, $opt);

            if ($current['driver'] == 'sqlite') {
                $this->connections[$name]->exec('PRAGMA foreign_keys = ON');
            }
        }

        return $this->connections[$name];
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->connection(), $name), $args);
    }
}
