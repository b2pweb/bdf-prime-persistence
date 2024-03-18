<?php

namespace Bdf\Prime\Persistence;

use Bdf\Prime\Persistence\Persister\RepositoryPersister;
use Bdf\Prime\Repository\RepositoryInterface;
use Doctrine\Persistence\ObjectRepository;

/**
 * Class PrimeObjectRepository
 */
final class PrimeObjectRepository implements ObjectRepository
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var RepositoryPersister
     */
    private $persister;


    /**
     * PrimeObjectRepository constructor.
     *
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->persister = new RepositoryPersister($repository);
    }

    /**
     * {@inheritdoc}
     */
    public function find($id): ?object
    {
        // @todo find from cached entity ?
        return $this->repository->queries()->findById($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return $this->repository->queries()->keyValue()->all();
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        $query = $this->repository->queries()->builder()->where($criteria);

        if ($orderBy) {
            $query->order($orderBy);
        }

        if ($limit) {
            $query->limit($limit);
        }

        if ($offset) {
            $query->offset($offset);
        }

        return $query->all();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria): ?object
    {
        return $this->repository->queries()->builder()->where($criteria)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName(): string
    {
        return $this->repository->entityClass();
    }

    /**
     * @internal
     * @return RepositoryPersister
     */
    public function getPersister(): RepositoryPersister
    {
        return $this->persister;
    }
}
