<?php

namespace Bdf\Prime\Persistence;

use Bdf\Prime\ServiceLocator;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;

/**
 * Adapt prime service locator to doctrine manager registry
 */
class PrimeManagerRegistry implements ManagerRegistry
{
    /**
     * @var ServiceLocator
     */
    private $serviceLocator;

    /**
     * @var PrimeObjectManager|null
     */
    private $manager;

    /**
     * PrimeManagerRegistry constructor.
     * @param ServiceLocator $serviceLocator
     */
    public function __construct(ServiceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultConnectionName(): string
    {
        return (string) $this->serviceLocator->connections()->getDefaultConnection();
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection($name = null): object
    {
        return $this->serviceLocator->connections()->getConnection($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getConnections(): array
    {
        return $this->serviceLocator->connections()->connections();
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectionNames(): array
    {
        return $this->serviceLocator->connections()->getConnectionNames();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultManagerName(): string
    {
        return 'prime';
    }

    /**
     * {@inheritdoc}
     */
    public function getManager($name = null): ObjectManager
    {
        if (!$this->manager) {
            $this->manager = new PrimeObjectManager($this->serviceLocator);
        }

        return $this->manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getManagers(): array
    {
        return [$this->getManager()];
    }

    /**
     * {@inheritdoc}
     */
    public function resetManager($name = null): ObjectManager
    {
        $this->manager = null;

        return $this->getManager($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getAliasNamespace($alias)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getManagerNames(): array
    {
        return ['prime'];
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository($persistentObject, $persistentManagerName = null): ObjectRepository
    {
        return $this->getManager()->getRepository($persistentObject);
    }

    /**
     * {@inheritdoc}
     */
    public function getManagerForClass($class): ?ObjectManager
    {
        return $this->getManager();
    }

    public function getName(): string
    {
        return 'prime';
    }
}
