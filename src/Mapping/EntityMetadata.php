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
    public function getName()
    {
        return $this->mapper->getEntityClass();
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->metadata->primary();
    }

    /**
     * {@inheritdoc}
     */
    public function getReflectionClass()
    {
        if (!$this->reflectionClass) {
            return $this->reflectionClass = new ReflectionClass($this->mapper->getEntityClass());
        }

        return $this->reflectionClass;
    }

    /**
     * {@inheritdoc}
     */
    public function isIdentifier($fieldName)
    {
        return $this->metadata->isPrimary($fieldName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasField($fieldName)
    {
        return $this->metadata->attributeExists($fieldName) || isset($this->metadata->embeddeds[$fieldName]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasAssociation($fieldName)
    {
        return isset($this->mapper->relations()[$fieldName]);
    }

    /**
     * {@inheritdoc}
     */
    public function isSingleValuedAssociation($fieldName)
    {
        return in_array(
            $this->mapper->relation($fieldName)['type'],
            [RelationInterface::BELONGS_TO, RelationInterface::HAS_ONE, RelationInterface::MORPH_TO, RelationInterface::BY_INHERITANCE]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCollectionValuedAssociation($fieldName)
    {
        return in_array(
            $this->mapper->relation($fieldName)['type'],
            [RelationInterface::HAS_MANY, RelationInterface::BELONGS_TO_MANY]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldNames()
    {
        return array_keys($this->metadata->attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifierFieldNames()
    {
        return $this->getIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociationNames()
    {
        return array_keys(iterator_to_array($this->mapper->relations()));
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeOfField($fieldName)
    {
        return $this->metadata->attributeType($fieldName);
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociationTargetClass($assocName)
    {
        return $this->mapper->relation($assocName)['entity'];
    }

    /**
     * {@inheritdoc}
     */
    public function isAssociationInverseSide($assocName)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociationMappedByTargetField($assocName)
    {
        return $this->mapper->relation($assocName)['localKey'];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifierValues($object)
    {
        // @todo filter empty values ?
        return $this->mapper->primaryCriteria($object);
    }
}
