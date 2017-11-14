<?php

namespace Sydes\Database;

use Doctrine\DBAL\Driver\PDOSqlite\Driver as DoctrineDriver;
use Sydes\Database\Query\Grammars\SQLiteGrammar as QueryGrammar;
use Sydes\Database\Query\Processors\SQLiteProcessor;
use Sydes\Database\Schema\Grammars\SQLiteGrammar as SchemaGrammar;
use Sydes\Database\Schema\SQLiteBuilder;

class SQLiteConnection extends Connection
{
    /**
     * Get the default query grammar instance.
     *
     * @return QueryGrammar
     */
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new QueryGrammar);
    }

    /**
     * Get a schema builder instance for the connection.
     *
     * @return SQLiteBuilder
     */
    public function getSchemaBuilder()
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new SQLiteBuilder($this);
    }

    /**
     * Get the default schema grammar instance.
     *
     * @return SchemaGrammar
     */
    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(new SchemaGrammar);
    }

    /**
     * Get the default post processor instance.
     *
     * @return SQLiteProcessor
     */
    protected function getDefaultPostProcessor()
    {
        return new SQLiteProcessor;
    }

    /**
     * Get the Doctrine DBAL driver.
     *
     * @return \Doctrine\DBAL\Driver\PDOSqlite\Driver
     */
    protected function getDoctrineDriver()
    {
        return new DoctrineDriver;
    }
}
