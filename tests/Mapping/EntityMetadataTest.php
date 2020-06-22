<?php

namespace Doctrine\Persistence\Mapping;

use Bdf\Prime\Entity\Model;
use Bdf\Prime\Persistence\AssocEntity;
use Bdf\Prime\Persistence\Mapping\EntityMetadata;
use Bdf\Prime\Persistence\OtherEntity;
use Bdf\Prime\Persistence\PrimeObjectManager;
use Bdf\Prime\Persistence\TestEntity;
use Bdf\Prime\Prime;
use Bdf\Prime\Test\TestPack;
use PHPUnit\Framework\TestCase;

/**
 * Class EntityMetadataTest
 */
class EntityMetadataTest extends TestCase
{
    /**
     * @var EntityMetadata
     */
    private $metadata;

    protected function setUp(): void
    {
        Prime::configure([
            'connection' => [
                'config' => [
                    'test' => 'sqlite::memory:'
                ],
            ],
        ]);
        Model::configure(function() { return Prime::service(); });

        $this->metadata = new EntityMetadata(TestEntity::mapper());
    }

    /**
     *
     */
    public function test_getName()
    {
        $this->assertEquals(TestEntity::class, $this->metadata->getName());
    }

    /**
     *
     */
    public function test_getIdentifier()
    {
        $this->assertEquals(['id'], $this->metadata->getIdentifier());
        $this->assertEquals(['id'], $this->metadata->getIdentifierFieldNames());
    }

    /**
     *
     */
    public function test_getReflectionClass()
    {
        $this->assertEquals(new \ReflectionClass(TestEntity::class), $this->metadata->getReflectionClass());
        $this->assertSame($this->metadata->getReflectionClass(), $this->metadata->getReflectionClass());
    }

    /**
     *
     */
    public function test_isIdentifier()
    {
        $this->assertTrue($this->metadata->isIdentifier('id'));
        $this->assertFalse($this->metadata->isIdentifier('value'));
    }

    /**
     *
     */
    public function test_hasField()
    {
        $this->assertTrue($this->metadata->hasField('id'));
        $this->assertTrue($this->metadata->hasField('value'));
        $this->assertFalse($this->metadata->hasField('not_found'));

        $this->assertTrue($this->metadata->hasField('assoc'));
        $this->assertFalse($this->metadata->hasField('detached'));
    }

    /**
     *
     */
    public function test_hasAssociation()
    {
        $this->assertTrue($this->metadata->hasAssociation('assoc'));
        $this->assertTrue($this->metadata->hasAssociation('detached'));
        $this->assertFalse($this->metadata->hasAssociation('not_found'));
    }

    /**
     *
     */
    public function test_isSingleValuedAssociation()
    {
        $this->assertTrue($this->metadata->isSingleValuedAssociation('assoc'));
        $this->assertFalse((new EntityMetadata(AssocEntity::mapper()))->isSingleValuedAssociation('entities'));
    }

    /**
     *
     */
    public function test_isCollectionValuedAssociation()
    {
        $this->assertFalse($this->metadata->isCollectionValuedAssociation('assoc'));
        $this->assertTrue((new EntityMetadata(AssocEntity::mapper()))->isCollectionValuedAssociation('entities'));
    }

    /**
     *
     */
    public function test_getFieldNames()
    {
        $this->assertEquals(['id', 'value'], $this->metadata->getFieldNames());
    }

    /**
     *
     */
    public function test_getAssociationNames()
    {
        $this->assertEquals(['assoc', 'detached'], $this->metadata->getAssociationNames());
    }

    /**
     *
     */
    public function test_getTypeOfField()
    {
        $this->assertEquals('integer', $this->metadata->getTypeOfField('id'));
        $this->assertEquals('string', $this->metadata->getTypeOfField('value'));
    }

    /**
     *
     */
    public function test_getAssociationTargetClass()
    {
        $this->assertEquals(AssocEntity::class, $this->metadata->getAssociationTargetClass('assoc'));
    }

    /**
     *
     */
    public function test_getAssociationMappedByTargetField()
    {
        $this->assertEquals('value', $this->metadata->getAssociationMappedByTargetField('assoc'));
    }

    /**
     *
     */
    public function test_getIdentifierValues()
    {
        $entity = new TestEntity();
        $entity->id = 5;

        $this->assertSame(['id' => 5], $this->metadata->getIdentifierValues($entity));
    }
}
