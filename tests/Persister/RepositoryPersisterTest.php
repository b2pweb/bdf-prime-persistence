<?php

namespace Bdf\Prime\Persistence\Persister;

use Bdf\Prime\Entity\Model;
use Bdf\Prime\Persistence\TestEntity;
use Bdf\Prime\Prime;
use Bdf\Prime\Test\TestPack;
use PHPUnit\Framework\TestCase;

/**
 * Class RepositoryPersisterTest
 */
class RepositoryPersisterTest extends TestCase
{
    /**
     * @var RepositoryPersister
     */
    private $persister;

    /**
     * @var TestPack
     */
    private $testPack;

    /**
     *
     */
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

        $this->persister = new RepositoryPersister(Prime::service()->repository(TestEntity::class));
        $this->testPack = new TestPack();
        $this->testPack->declareEntity([TestEntity::class])->initialize();
    }

    protected function tearDown(): void
    {
        $this->testPack->destroy();
    }

    /**
     *
     */
    public function test_add()
    {
        $entity = new TestEntity('foo');
        $this->persister->add($entity);

        $this->assertNull($entity->id);
        $this->persister->flush();

        $this->assertEquals(1, $entity->id);
        $this->assertEquals($entity, TestEntity::refresh($entity));

        $entity->value = 'bar';
        $this->persister->add($entity);
        $this->persister->flush();

        $this->assertEquals('bar', TestEntity::refresh($entity)->value);
    }

    /**
     *
     */
    public function test_add_multiple()
    {
        for ($i = 0; $i < 10; ++$i) {
            $this->persister->add(new TestEntity('foo'));
        }

        $this->assertEquals(0, TestEntity::count());

        $this->persister->flush();
        $this->assertEquals(10, TestEntity::count());
    }

    /**
     *
     */
    public function test_remove()
    {
        $entity = new TestEntity('foo');
        $entity->insert();

        $this->persister->remove($entity);
        $this->assertNotNull(TestEntity::refresh($entity));

        $this->persister->flush();
        $this->assertNull(TestEntity::refresh($entity));
    }

    /**
     *
     */
    public function test_add_then_remove_should_do_nothing()
    {
        $entity = new TestEntity('foo');

        $this->persister->add($entity);
        $this->persister->remove($entity);

        $this->persister->flush();
        $this->assertEquals(0, TestEntity::count());
    }

    /**
     *
     */
    public function test_detach()
    {
        $entity = new TestEntity('foo');
        $this->persister->add($entity);
        $this->persister->detach($entity);

        $this->persister->flush();

        $this->assertNull($entity->id);
    }

    /**
     *
     */
    public function test_cancel()
    {
        $this->persister->add(new TestEntity('foo'));
        $this->persister->cancel();
        $this->persister->flush();

        $this->assertEquals(0, TestEntity::count());
    }
    /**
     *
     */
    public function test_contains()
    {
        $entity = new TestEntity('foo');

        $this->assertFalse($this->persister->contains($entity));
        $this->persister->add($entity);
        $this->assertTrue($this->persister->contains($entity));
    }
}
