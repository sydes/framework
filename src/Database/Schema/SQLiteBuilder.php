<?php

namespace Sydes\Database\Schema;

class SQLiteBuilder extends Builder
{
    /**
     * Drop all tables from the database.
     */
    public function dropAllTables()
    {
        if ($this->connection->getDatabaseName() != ':memory:') {
            return $this->refreshDatabaseFile();
        }

        $this->connection->select($this->grammar->compileEnableWriteableSchema());

        $this->connection->select($this->grammar->compileDropAllTables());

        $this->connection->select($this->grammar->compileDisableWriteableSchema());
    }

    /**
     * Delete the database file & re-create it.
     */
    public function refreshDatabaseFile()
    {
        unlink($this->connection->getDatabaseName());

        touch($this->connection->getDatabaseName());
    }
}
