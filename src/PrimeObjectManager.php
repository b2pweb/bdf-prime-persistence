<?php

namespace Bdf\Prime\Persistence;

use Bdf\Prime\Persistence\Mapping\EntityMetadata;
use Bdf\Prime\Persistence\Mapping\PrimeMetadataFactory;
use Bdf\Prime\ServiceLocator;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;

/**
 * Adapt prime service locator to doctrine object manager
 */
final class PrimeObjectManager implements ObjectManager
{
    /**
     * @var ServiceLocator
     */
    private $serviceLocator;

    /**
     * @var PrimeObjectRepository[]
     */
    private $repositories = [];

    /**
     * @var PrimeMetadataFactory|null
     */
    private $metadataFactory;

    /**
     * PrimeObjectManager constructor.
     *
     * @param ServiceLocator $serviceLocator
     */
    public function __construct(ServiceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function find($className, $id): ?object
    {
        return $this->getRepository($className)->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function persist($object): void
    {
        $this->getRepository(get_class($object))->getPersister()->add($object);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($object): void
    {
        $this->getRepository(get_class($object))->getPersister()->remove($object);
    }

    /**
     * {@inheritdoc}
     */
    public function merge($object)
    {
        throw new \BadMethodCallException('Unsupported operation');
    }

    /**
     * {@inheritdoc}
     */
    public function clear($objectName = null): void
    {
        if ($objectName === null) {
            foreach ($this->repositories as $repository) {
                $repository->getPersister()->cancel();
            }
        } elseif (isset($this->repositories[$objectName])) {
            $this->repositories[$objectName]->getPersister()->cancel();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function detach($object): void
    {
        $this->getRepository(get_class($object))->getPersister()->detach($object);
    }

    /**
     * {@inheritdoc}
     */
    public function refresh($object): void
    {
        throw new \BadMethodCallException('Unsupported operation');
    }

    /**
     * {@inheritdoc}
     */
    public function flush(): void
    {
        foreach ($this->repositories as $repository) {
            $repository->getPersister()->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository($className): ?ObjectRepository
    {
        if (isset($this->repositories[$className])) {
            return $this->repositories[$className];
        }

        return $this->repositories[$className] = new PrimeObjectRepository($this->serviceLocator->repository($className));
    }

    /**
     * {@inheritdoc}
     */
    public function getClassMetadata($className): ClassMetadata
    {
        return $this->getMetadataFactory()->getMetadataFor($className);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataFactory(): ClassMetadataFactory
    {
        if ($this->metadataFactory) {
            return $this->metadataFactory;
        }

        return $this->metadataFactory = new PrimeMetadataFactory($this->serviceLocator);
    }

    /**
     * {@inheritdoc}
     */
    public function initializeObject($obj): void
    {
        throw new \BadMethodCallException('Unsupported operation');
    }

    /**
     * {@inheritdoc}
     */
    public function contains($object): bool
    {
        $className = get_class($object);
        return isset($this->repositories[$className]) && $this->repositories[$className]->getPersister()->contains($object);
    }

    /**
     * @return ServiceLocator
     */
    public function serviceLocator(): ServiceLocator
    {
        return $this->serviceLocator;
    }
}
