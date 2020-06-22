<?php

namespace Bdf\Prime\Persistence\Mapping;

use Bdf\Prime\ServiceLocator;
use Bdf\Util\File\ClassFileLocator;
use Doctrine\Persistence\Mapping\ClassMetadataFactory;

/**
 * Class PrimeMetadataFactory
 */
final class PrimeMetadataFactory implements ClassMetadataFactory
{
    /**
     * @var ServiceLocator
     */
    private $serviceLocator;

    /**
     * @var ClassFileLocator|null
     */
    private $classFileLocator;


    /**
     * PrimeMetadataFactory constructor.
     *
     * @param ServiceLocator $serviceLocator
     * @param ClassFileLocator|null $classFileLocator The iterator for locate entity classes
     */
    public function __construct(ServiceLocator $serviceLocator, ?ClassFileLocator $classFileLocator = null)
    {
        $this->serviceLocator = $serviceLocator;
        $this->classFileLocator = $classFileLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllMetadata()
    {
        if ($this->classFileLocator === null) {
            return array_map([$this, 'getMetadataFor'], $this->serviceLocator->repositoryNames());
        }

        $all = [];

        foreach ($this->classFileLocator as $class) {
            if ($repository = $this->serviceLocator->repository($class->getClass())) {
                $all[] = new EntityMetadata($repository->mapper());
            }
        }

        $this->classFileLocator = null; // All classes are already loaded : the locator is not useful

        return $all;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataFor($className)
    {
        return new EntityMetadata($this->serviceLocator->repository($className)->mapper());
    }

    /**
     * {@inheritdoc}
     */
    public function hasMetadataFor($className)
    {
        return $this->serviceLocator->repository($className) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadataFor($className, $class)
    {
        throw new \BadMethodCallException('Cannot set metadata');
    }

    /**
     * {@inheritdoc}
     */
    public function isTransient($className)
    {
        return $this->hasMetadataFor($className);
    }
}
