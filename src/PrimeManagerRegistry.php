<?php

namespace Bdf\Prime\Persistence;

use Bdf\Prime\ServiceLocator;
use Doctrine\Persistence\ManagerRegistry;

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
    public function getDefaultConnectionName()
    {
        return $this->serviceLocator->connections()->getDefaultConnection();
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection($name = null)
    {
        return $this->serviceLocator->connections()->getConnection($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getConnections()
    {
        return $this->serviceLocator->connections()->connections();
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectionNames()
    {
        return $this->serviceLocator->connections()->getConnectionNames();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultManagerName()
    {
        return 'prime';
    }

    /**
     * {@inheritdoc}
     */
    public function getManager($name = null)
    {
        if (!$this->manager) {
            $this->manager = new PrimeObjectManager($this->serviceLocator);
        }

        return $this->manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getManagers()
    {
        return [$this->getManager()];
    }

    /**
     * {@inheritdoc}
     */
    public function resetManager($name = null)
    {
        $this->manager = null;
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
    public function getManagerNames()
    {
        return ['prime'];
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository($persistentObject, $persistentManagerName = null)
    {
        return $this->getManager()->getRepository($persistentObject);
    }

    /**
     * {@inheritdoc}
     */
    public function getManagerForClass($class)
    {
        return $this->getManager();
    }

    public function getName(): string
    {
        return 'prime';
    }
}
