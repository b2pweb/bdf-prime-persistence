<?php

namespace Bdf\Prime\Persistence;

use Bdf\Prime\Collection\EntityCollection;
use Bdf\Prime\Entity\Model;
use Bdf\Prime\Persistence\Persister\RepositoryPersister;
use Bdf\Prime\Prime;
use Bdf\Prime\Test\TestPack;
use PHPUnit\Framework\TestCase;

/**
 * Class PrimeObjectRepositoryTest
 */
class PrimeObjectRepositoryTest extends TestCase
{
    /**
     * @var PrimeObjectRepository
     */
    private $objectRepository;

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

        $this->objectRepository = new PrimeObjectRepository(Prime::service()->repository(TestEntity::class));
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
    public function test_find()
    {
        $entity = new TestEntity('foo');
        $entity->insert();

        $this->assertNull($this->objectRepository->find(404));
        $this->assertEquals($entity, $this->objectRepository->find($entity->id));
    }

    /**
     *
     */
    public function test_findAll()
    {
        $entities = [
            new TestEntity('foo'),
            new TestEntity('bar'),
        ];

        TestEntity::collection($entities)->save();

        $this->assertEquals($entities, $this->objectRepository->findAll());
    }

    /**
     *
     */
    public function test_findOneBy()
    {
        $entity = new TestEntity('foo');
        $entity->insert();

        $this->assertEquals($entity, $this->objectRepository->findOneBy(['value' => 'foo']));
        $this->assertNull($this->objectRepository->findOneBy(['value' => 'bar']));
    }

    /**
     *
     */
    public function test_findBy()
    {
        $foo = new TestEntity('foo');
        $bar = new TestEntity('bar');
        $foo->insert();
        $bar->insert();

        $this->assertEquals([$foo], $this->objectRepository->findBy(['value' => 'foo']));
        $this->assertEquals([$bar, $foo], $this->objectRepository->findBy([], ['value']));
        $this->assertEquals([$bar], $this->objectRepository->findBy([], ['value'], 1));
        $this->assertEquals([$foo], $this->objectRepository->findBy([], ['value'], 1, 1));
    }

    /**
     *
     */
    public function test_getClassName()
    {
        $this->assertEquals(TestEntity::class, $this->objectRepository->getClassName());
    }

    /**
     *
     */
    public function test_getPersister()
    {
        $this->assertInstanceOf(RepositoryPersister::class, $this->objectRepository->getPersister());
    }
}
