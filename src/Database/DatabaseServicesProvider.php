<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Database;

use Sydes\Database\Entity\Builder;
use Sydes\Database\Entity\Manager;
use Sydes\Database\Entity\Model;
use Sydes\Services\ServiceProviderInterface;

class DatabaseServicesProvider implements ServiceProviderInterface
{
    public function register(\DI\Container $c)
    {
        $c->set(Connection::class, function ($c) {
            $path = $c->get('dir.site.this').'/database.db';
            $pdo = new \PDO('sqlite:'.$path);
            $con = new \Sydes\Database\SQLiteConnection($pdo, $path);

            $con->getSchemaBuilder()->enableForeignKeyConstraints();

            Model::setLocales($c->get('site')->get('locales'));
            Model::setFieldTypes($c->get('entity.fieldTypes'));
            Model::setQuery(new Builder(new \Sydes\Database\Query\Builder($con)));

            return $con;
        });

        $c->set(Manager::class, function ($c) {
            return new Manager($c->get('db'));
        });
    }
}
