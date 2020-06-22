<?php

namespace Bdf\Prime\Persistence\Persister;

use Bdf\Prime\Repository\RepositoryInterface;
use Bdf\Prime\Repository\Write\BufferedWriter;

/**
 * Handle persistence for a single repository
 */
final class RepositoryPersister
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var PersistEntity[]
     */
    private $entities = [];

    /**
     * RepositoryPersister constructor.
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Add a new entity to persist
     *
     * @param object $entity
     */
    public function add($entity): void
    {
        $this->push($entity, PersistEntity::OPERATION_PERSIST);
    }

    /**
     * Remove the entity from database
     *
     * @param object $entity
     */
    public function remove($entity): void
    {
        $this->push($entity, PersistEntity::OPERATION_REMOVE);
    }

    /**
     * Detach the entity. Pending operation of the entity will be cancelled
     *
     * @param object $entity
     */
    public function detach($entity): void
    {
        unset($this->entities[spl_object_hash($entity)]);
    }

    /**
     * Apply all pending operations to database
     */
    public function flush(): void
    {
        if (empty($this->entities)) {
            return;
        }

        $writer = new BufferedWriter($this->repository);

        foreach ($this->entities as $entity) {
            switch ($entity->operation) {
                case PersistEntity::OPERATION_PERSIST:
                    if ($this->repository->isNew($entity->entity)) {
                        $writer->insert($entity->entity);
                    } else {
                        $writer->update($entity->entity);
                    }
                    break;

                case PersistEntity::OPERATION_REMOVE:
                    if (!$this->repository->isNew($entity->entity)) {
                        $writer->delete($entity->entity);
                    }
                    break;
            }
        }

        $writer->flush();

        $this->entities = [];
    }

    /**
     * Cancel all pending operations
     */
    public function cancel(): void
    {
        $this->entities = [];
    }

    /**
     * Check if the persist contains the given entity
     *
     * @param object $entity
     *
     * @return bool
     */
    public function contains($entity): bool
    {
        return isset($this->entities[spl_object_hash($entity)]);
    }

    private function push($entity, string $operation): void
    {
        $id = spl_object_hash($entity);

        if (isset($this->entities[$id])) {
            $this->entities[$id]->operation = $operation;
            return;
        }

        $this->entities[$id] = new PersistEntity($entity, $operation);
    }
}
