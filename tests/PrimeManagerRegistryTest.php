<?php

namespace Bdf\Prime\Persistence;

use Bdf\Prime\Connection\SimpleConnection;
use Bdf\Prime\Entity\Model;
use Bdf\Prime\Prime;
use PHPUnit\Framework\TestCase;

/**
 * Class PrimeManagerRegistryTest
 */
class PrimeManagerRegistryTest extends TestCase
{
    /**
     * @var PrimeManagerRegistry
     */
    private $registry;

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

        $this->registry = new PrimeManagerRegistry(Prime::service());
    }

    /**
     *
     */
    public function test_getDefaultConnectionName()
    {
        Prime::service()->connections()->setDefaultConnection('test');
        $this->assertEquals('test', $this->registry->getDefaultConnectionName());
    }

    /**
     *
     */
    public function test_getConnection()
    {
        $this->assertInstanceOf(SimpleConnection::class, $this->registry->getConnection('test'));
    }

    /**
     *
     */
    public function test_getConnectionNames()
    {
        $this->assertEquals(['test'], $this->registry->getConnectionNames());
    }

    /**
     *
     */
    public function test_getConnections()
    {
        $this->assertEmpty($this->registry->getConnections());
        $this->registry->getConnection('test');

        $this->assertEquals(['test' => $this->registry->getConnection('test')], $this->registry->getConnections());
    }

    /**
     *
     */
    public function test_getDefaultManagerName()
    {
        $this->assertEquals('prime', $this->registry->getDefaultManagerName());
    }

    /**
     *
     */
    public function test_getManager()
    {
        $this->assertInstanceOf(PrimeObjectManager::class, $this->registry->getManager());
        $this->assertSame($this->registry->getManager(), $this->registry->getManager());
    }

    /**
     *
     */
    public function test_getManagers()
    {
        $this->assertSame([$this->registry->getManager()], $this->registry->getManagers());
    }

    /**
     *
     */
    public function test_resetManager()
    {
        $manager = $this->registry->getManager();
        $this->registry->resetManager();

        $this->assertNotSame($manager, $this->registry->getManager());
    }

    /**
     *
     */
    public function test_getRepository()
    {
        $this->assertInstanceOf(PrimeObjectRepository::class, $this->registry->getRepository(TestEntity::class));
        $this->assertEquals(TestEntity::class, $this->registry->getRepository(TestEntity::class)->getClassName());
    }
}
