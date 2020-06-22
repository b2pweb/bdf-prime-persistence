<?php

namespace Bdf\Prime\Persistence;

use Bdf\Prime\Entity\Model;
use Bdf\Prime\Persistence\Mapping\EntityMetadata;
use Bdf\Prime\Prime;
use Bdf\Prime\Test\TestPack;
use PHPUnit\Framework\TestCase;

class PrimeObjectManagerTest extends TestCase
{
    /**
     * @var PrimeObjectManager
     */
    private $objectManager;

    /**
     * @var TestPack
     */
    private $testPack;

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

        $this->objectManager = new PrimeObjectManager(Prime::service());
        $this->testPack = new TestPack();
        $this->testPack->declareEntity([TestEntity::class, OtherEntity::class])->initialize();
    }

    protected function tearDown(): void
    {
        $this->testPack->destroy();
    }

    /**
     *
     */
    public function test_persist()
    {
        $entity = new TestEntity('foo');
        $this->objectManager->persist($entity);

        $this->assertNull($entity->id);
        $this->objectManager->flush();

        $this->assertEquals(1, $entity->id);
        $this->assertEquals($entity, TestEntity::refresh($entity));

        $entity->value = 'bar';
        $this->objectManager->persist($entity);
        $this->objectManager->flush();

        $this->assertEquals('bar', TestEntity::refresh($entity)->value);
    }

    /**
     *
     */
    public function test_persist_multiple()
    {
        for ($i = 0; $i < 10; ++$i) {
            $this->objectManager->persist(new TestEntity('foo'));
        }

        $this->assertEquals(0, TestEntity::count());

        $this->objectManager->flush();
        $this->assertEquals(10, TestEntity::count());
    }

    /**
     *
     */
    public function test_persist_multiple_repositories()
    {
        $this->objectManager->persist(new TestEntity('foo'));
        $this->objectManager->persist(new OtherEntity());
        $this->objectManager->flush();

        $this->assertEquals(1, TestEntity::count());
        $this->assertEquals(1, OtherEntity::count());
    }

    /**
     *
     */
    public function test_remove()
    {
        $entity = new TestEntity('foo');
        $entity->insert();

        $this->objectManager->remove($entity);
        $this->assertNotNull(TestEntity::refresh($entity));

        $this->objectManager->flush();
        $this->assertNull(TestEntity::refresh($entity));
    }

    /**
     *
     */
    public function test_detach()
    {
        $entity = new TestEntity('foo');
        $this->objectManager->persist($entity);
        $this->objectManager->detach($entity);

        $this->objectManager->flush();

        $this->assertNull($entity->id);
    }

    /**
     *
     */
    public function test_clear_all()
    {
        $this->objectManager->persist(new TestEntity('foo'));
        $this->objectManager->persist(new OtherEntity());
        $this->objectManager->clear();

        $this->objectManager->flush();

        $this->assertEquals(0, TestEntity::count());
        $this->assertEquals(0, OtherEntity::count());
    }

    /**
     *
     */
    public function test_clear_entity()
    {
        $this->objectManager->persist(new TestEntity('foo'));
        $this->objectManager->persist(new OtherEntity());
        $this->objectManager->clear(TestEntity::class);

        $this->objectManager->flush();

        $this->assertEquals(0, TestEntity::count());
        $this->assertEquals(1, OtherEntity::count());
    }

    /**
     *
     */
    public function test_find()
    {
        $this->assertNull($this->objectManager->find(TestEntity::class, 404));
        $entity = new TestEntity('foo');
        $entity->insert();
        $this->assertEquals($entity, $this->objectManager->find(TestEntity::class, $entity->id));
    }

    /**
     *
     */
    public function test_getRepository()
    {
        $this->assertInstanceOf(PrimeObjectRepository::class, $this->objectManager->getRepository(TestEntity::class));
        $this->assertEquals(TestEntity::class, $this->objectManager->getRepository(TestEntity::class)->getClassName());
    }

    /**
     *
     */
    public function test_getClassMetadata()
    {
        $this->assertInstanceOf(EntityMetadata::class, $this->objectManager->getClassMetadata(TestEntity::class));
        $this->assertEquals(TestEntity::class, $this->objectManager->getClassMetadata(TestEntity::class)->getName());
    }

    /**
     *
     */
    public function test_contains()
    {
        $entity = new TestEntity('foo');

        $this->assertFalse($this->objectManager->contains($entity));
        $this->objectManager->persist($entity);
        $this->assertTrue($this->objectManager->contains($entity));
    }
}
