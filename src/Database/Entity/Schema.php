<?php
/**
 * @link      https://github.com/sydes/framework
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   MIT license; see LICENSE
 */

namespace Sydes\Database\Entity;

use Sydes\Database\Schema\Blueprint;

class Schema
{
    use Concerns\InteractsWithModel;

    /**
     * @var Manager
     */
    protected $em;

    public function __construct(Manager $em)
    {
        $this->em = $em;
    }

    /**
     * Create tables for this entity
     */
    public function create()
    {
        $table = $this->model->getTable();
        $conn = $this->em->getConnection();
        $schema = $conn->getSchemaBuilder();
        $main = new Event;
        $translated = new Event;

        foreach ($this->model->getFields() as $key => $field) {
            $field->getEventListeners($this->model->isTranslatable($key) ? $translated : $main);
        }

        $schema->create($table, function (Blueprint $t) use ($main, $conn) {
            if ($this->model->hasIncrementing()) {
                $t->increments($this->model->getKeyName());
            }

            $main->createTable($t, $conn);
        });

        if ($this->model->hasTranslatable()) {
            $schema->create($this->model->getTranslationTable(), function (Blueprint $t) use ($table, $translated, $conn) {
                $foreign = $this->model->getForeignKey();

                if ($this->model->hasIncrementing()) {
                    $t->integer($foreign)->unsigned();
                } else {
                    $t->string($foreign);
                }

                $t->string('locale');

                $translated->createTable($t, $conn);

                $t->foreign($foreign)->references($this->model->getKeyName())->on($table)->onDelete('cascade');
                $t->primary([$foreign, 'locale']);
            });
        }
    }

    /**
     * Rename a table on the schema.
     *
     * @param string $from current table name
     */
    public function rename($from)
    {
        $this->em->getConnection()->getSchemaBuilder()->rename($from, $this->model->getTable());
    }

    /**
     * Drop tables with this entity
     */
    public function drop()
    {
        $conn = $this->em->getConnection();
        $this->model->fillEvents(new Event)->fire('drop', $conn);

        $schema = $conn->getSchemaBuilder();

        if ($this->model->hasTranslatable()) {
            $schema->drop($this->model->getTranslationTable());
        }

        $schema->drop($this->model->getTable());
    }

    /**
     * Update table to new state of this entity
     */
    public function update()
    {
        // TODO find diff (actual/new_in_model) and make sql
    }
}
