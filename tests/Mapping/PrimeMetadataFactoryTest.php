<?php

namespace Bdf\Prime\Persistence\Mapping;

use Bdf\Prime\Entity\Model;
use Bdf\Prime\Persistence\AssocEntity;
use Bdf\Prime\Persistence\OtherEntity;
use Bdf\Prime\Persistence\TestEntity;
use Bdf\Prime\Prime;
use Bdf\Util\File\ClassFileLocator;
use PHPUnit\Framework\TestCase;

/**
 * Class PrimeMetadataFactoryTest
 */
class PrimeMetadataFactoryTest extends TestCase
{
    /**
     * @var PrimeMetadataFactory
     */
    private $factory;

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

        $this->factory = new PrimeMetadataFactory(Prime::service());
    }

    /**
     *
     */
    public function test_hasMetadataFor()
    {
        $this->assertTrue($this->factory->hasMetadataFor(TestEntity::class));
        $this->assertTrue($this->factory->hasMetadataFor(OtherEntity::class));
        $this->assertFalse($this->factory->hasMetadataFor(\ArrayIterator::class));
    }

    /**
     *
     */
    public function test_isTransient()
    {
        $this->assertTrue($this->factory->isTransient(TestEntity::class));
        $this->assertTrue($this->factory->isTransient(OtherEntity::class));
        $this->assertFalse($this->factory->isTransient(\ArrayIterator::class));
    }

    /**
     *
     */
    public function test_getMetadataFor()
    {
        $this->assertInstanceOf(EntityMetadata::class, $this->factory->getMetadataFor(TestEntity::class));
        $this->assertEquals(TestEntity::class, $this->factory->getMetadataFor(TestEntity::class)->getName());
    }

    /**
     *
     */
    public function test_getAllMetadata()
    {
        $this->assertEquals([
            $this->factory->getMetadataFor(TestEntity::class),
            $this->factory->getMetadataFor(OtherEntity::class),
            $this->factory->getMetadataFor(AssocEntity::class),
        ], $this->factory->getAllMetadata());
    }

    /**
     *
     */
    public function test_getAllMetadata_with_locator()
    {
        $this->factory = new PrimeMetadataFactory(Prime::service(), new ClassFileLocator(__DIR__.'/../_files'));
        $all = $this->factory->getAllMetadata();

        $this->assertEqualsCanonicalizing([
            $this->factory->getMetadataFor(AssocEntity::class),
            $this->factory->getMetadataFor(TestEntity::class),
            $this->factory->getMetadataFor(OtherEntity::class),
        ], $all);
    }
}
