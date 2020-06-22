<?php

namespace Bdf\Prime\Persistence\Persister;

/**
 * Structure for attached entities on RepositoryPersister
 */
final class PersistEntity
{
    const OPERATION_PERSIST = 'persist';
    const OPERATION_REMOVE = 'remove';

    /**
     * @var object
     */
    public $entity;

    /**
     * @var string
     */
    public $operation;

    /**
     * PersistEntity constructor.
     * @param object $entity
     * @param string $operation
     */
    public function __construct($entity, string $operation)
    {
        $this->entity = $entity;
        $this->operation = $operation;
    }
}
