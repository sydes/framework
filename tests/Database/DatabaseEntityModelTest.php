<?php

namespace Sydes\Tests\Database;

use PHPUnit\Framework\TestCase;
use Sydes\Database\Connection;
use Sydes\Database\Entity\Field;
use Sydes\Database\Entity\Manager;
use Sydes\Database\Entity\Model;
use Mockery as M;

/**
 * @covers Model
 */
final class DatabaseEntityModelTest extends TestCase
{
    /** @var Manager */
    private $em;

    public function setUp()
    {
        /** @var Connection $conn */
        $conn = M::mock(Connection::class);
        $this->em = new Manager($conn);
        Model::setFieldTypes([
            'Text' => TextField::class,
        ]);
    }

    public function tearDown()
    {
        M::close();
    }

    public function testSerializable()
    {
        $structure = [
            'table' => 'stub',
            'fields' => [
                'title' => [
                    'type' => 'Text'
                ]
            ],
            'panels' => [
                'main' => [
                    'type' => 'Field',
                    'items' => 'title',
                ]
            ]
        ];

        $model = $this->em->make($structure);

        $this->assertInstanceOf(Model::class, $model);
        $this->assertSame($structure, $this->em->getStructure($model));
    }

    public function testTableName()
    {
        $post = new Post();
        $this->assertSame('posts', $post->getTable());

        $model = $this->em->make([]);
        $this->assertSame('models', $model->getTable());

        $model = $this->em->make(['table' => 'stub']);
        $this->assertSame('stub', $model->getTable());

        $model = new Model;
        $model->setTable('test');
        $this->assertSame('test', $model->getTable());
    }

    public function testPrimaryKey()
    {
        $model = $this->em->make([
            'fields' => [
                'title' => [
                    'type' => 'Text'
                ]
            ]
        ]);

        $this->assertSame('id', $model->getKeyName());
        $this->assertTrue($model->hasIncrementing());

        $model = $this->em->make([
            'fields' => [
                'code' => [
                    'type' => 'Primary'
                ]
            ]
        ]);

        $this->assertSame('code', $model->getKeyName());
        $this->assertFalse($model->hasIncrementing());
    }

    public function testFields()
    {
        $model = $this->em->make([
            'fields' => [
                'title' => [
                    'type' => 'Text'
                ]
            ]
        ]);

        $this->assertTrue($model->hasField('title'));
        $this->assertFalse($model->hasField('content'));

        $model->addField('content', [
            'type' => 'Text'
        ]);

        $this->assertTrue($model->hasField('content'));

        $fields = $model->getFields();

        $this->assertEquals(2, count($fields));
        $this->assertInstanceOf(Field::class, $fields['content']);
    }

    public function testExtendedFields()
    {
        $model = $this->em->make([
            'fields' => [
                'comments' => [
                    'type' => 'EntityRelation',
                ],
                'title' => [
                    'type' => 'Text',
                    'settings' => [
                        'translatable' => true,
                    ],
                ]
            ]
        ]);

        $this->assertSame(['comments'], $model->getRelationalFields());
        $this->assertSame(['title'], $model->getTranslatableFields());
    }

    public function testExistingFieldForPanel()
    {
        return; // TODO make

        $this->expectException(\InvalidArgumentException::class);

        $structure = [
            'fields' => [
                'title' => [
                    'type' => 'Text'
                ]
            ],
            'panels' => [
                'main' => [
                    'type' => 'Field',
                    'items' => 'content',
                ]
            ]
        ];

        $this->em->make($structure);
    }

    public function testPanels()
    {
        $structure = [
            'fields' => [
                'title' => [
                    'type' => 'Text'
                ]
            ],
            'panels' => [
                'main' => [
                    'type' => 'Field',
                    'items' => 'title',
                ]
            ]
        ];

        $model = $this->em->make($structure);

        $model->getPanels();
    }
}

class Post extends Model
{
}

class TextField extends Field
{
}
