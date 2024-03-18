<?php

namespace Bdf\Prime\Persistence\Mapping;

use Bdf\Prime\Mapper\Mapper;
use Bdf\Prime\Mapper\Metadata;
use Bdf\Prime\Relations\RelationInterface;
use Doctrine\Persistence\Mapping\ClassMetadata;
use ReflectionClass;

/**
 * Class EntityMetadata
 */
final class EntityMetadata implements ClassMetadata
{
    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * @var Metadata
     */
    private $metadata;

    /**
     * @var ReflectionClass
     */
    private $reflectionClass;

    /**
     * EntityMetadata constructor.
     *
     * @param Mapper $mapper
     */
    public function __construct(Mapper $mapper)
    {
        $this->mapper = $mapper;
        $this->metadata = $mapper->metadata();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->mapper->getEntityClass();
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): array
    {
        return $this->metadata->primary();
    }

    /**
     * {@inheritdoc}
     */
    public function getReflectionClass(): ReflectionClass
    {
        if (!$this->reflectionClass) {
            return $this->reflectionClass = new ReflectionClass($this->mapper->getEntityClass());
        }

        return $this->reflectionClass;
    }

    /**
     * {@inheritdoc}
     */
    public function isIdentifier($fieldName): bool
    {
        return $this->metadata->isPrimary($fieldName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasField($fieldName): bool
    {
        return $this->metadata->attributeExists($fieldName) || isset($this->metadata->embeddeds[$fieldName]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasAssociation($fieldName): bool
    {
        return isset($this->mapper->relations()[$fieldName]);
    }

    /**
     * {@inheritdoc}
     */
    public function isSingleValuedAssociation($fieldName): bool
    {
        return in_array(
            $this->mapper->relation($fieldName)['type'],
            [RelationInterface::BELONGS_TO, RelationInterface::HAS_ONE, RelationInterface::MORPH_TO, RelationInterface::BY_INHERITANCE]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCollectionValuedAssociation($fieldName): bool
    {
        return in_array(
            $this->mapper->relation($fieldName)['type'],
            [RelationInterface::HAS_MANY, RelationInterface::BELONGS_TO_MANY]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldNames(): array
    {
        return array_keys($this->metadata->attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifierFieldNames(): array
    {
        return $this->getIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociationNames(): array
    {
        $relations = $this->mapper->relations();

        if (!is_array($relations)) {
            $relations = iterator_to_array($relations);
        }

        return array_keys($relations);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeOfField($fieldName): ?string
    {
        return $this->metadata->attributeType($fieldName);
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociationTargetClass($assocName): ?string
    {
        return $this->mapper->relation($assocName)['entity'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function isAssociationInverseSide($assocName): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociationMappedByTargetField($assocName): string
    {
        return $this->mapper->relation($assocName)['localKey'];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifierValues($object): array
    {
        // @todo filter empty values ?
        return $this->mapper->primaryCriteria($object);
    }
}
