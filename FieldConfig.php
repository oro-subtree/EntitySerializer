<?php

namespace Oro\Component\EntitySerializer;

use Symfony\Component\Form\DataTransformerInterface as FormDataTransformerInterface;

class FieldConfig
{
    /** a flag indicates whether the field should be excluded */
    const EXCLUDE = 'exclude';

    /**
     * a flag indicates whether the target entity should be collapsed;
     * it means that target entity should be returned a a value, instead of an array;
     * usually it is used to get identifier of an entity
     */
    const COLLAPSE = 'collapse';

    /** the path of the field value */
    const PROPERTY_PATH = 'property_path';

    /** the data transformer to be applies to the field value */
    const DATA_TRANSFORMER = 'data_transformer';

    /** the property path of the field which can be used to get the discriminator value of the entity */
    const METADATA_DISCRIMINATOR = '__discriminator__';

    /** the property path of the field which can be used to get FQCN of the entity */
    const METADATA_CLASS_NAME = '__class__';

    /** @var array */
    protected $items = [];

    /** @var EntityConfig|null */
    private $targetEntity;

    /**
     * Gets a native PHP array representation of the field configuration.
     *
     * @param bool $excludeTargetEntity
     *
     * @return array
     */
    public function toArray($excludeTargetEntity = false)
    {
        $result = $this->items;

        if (!$excludeTargetEntity && null !== $this->targetEntity) {
            $result = array_merge($result, $this->targetEntity->toArray());
        }

        return $result;
    }

    /**
     * Indicates whether the field does not have a configuration.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return
            empty($this->items)
            && (null === $this->targetEntity || $this->targetEntity->isEmpty());
    }

    /**
     * Gets the configuration of the target entity if the field represents an association with to another entity.
     *
     * @return EntityConfig|null
     */
    public function getTargetEntity()
    {
        return $this->targetEntity;
    }

    /**
     * Sets the configuration of the target entity if the field represents an association with to another entity.
     *
     * @param EntityConfig|null $targetEntity
     *
     * @return EntityConfig|null
     */
    public function setTargetEntity(EntityConfig $targetEntity = null)
    {
        $this->targetEntity = $targetEntity;

        return $this->targetEntity;
    }

    /**
     * Indicates whether the field should be excluded.
     *
     * @return bool
     */
    public function isExcluded()
    {
        return array_key_exists(self::EXCLUDE, $this->items)
            ? $this->items[self::EXCLUDE]
            : false;
    }

    /**
     * Sets a flag indicates whether the field should be excluded.
     *
     * @param bool $exclude
     */
    public function setExcluded($exclude = true)
    {
        if ($exclude) {
            $this->items[self::EXCLUDE] = $exclude;
        } else {
            unset($this->items[self::EXCLUDE]);
        }
    }

    /**
     * Indicates whether the target entity should be collapsed.
     *
     * @return bool
     */
    public function isCollapsed()
    {
        return array_key_exists(self::COLLAPSE, $this->items)
            ? $this->items[self::COLLAPSE]
            : false;
    }

    /**
     * Sets a flag indicates whether the target entity should be collapsed.
     *
     * @param bool $collapse
     */
    public function setCollapsed($collapse = true)
    {
        if ($collapse) {
            $this->items[self::COLLAPSE] = $collapse;
        } else {
            unset($this->items[self::COLLAPSE]);
        }
    }

    /**
     * Indicates whether the path of the field value exists.
     *
     * @return string
     */
    public function hasPropertyPath()
    {
        return array_key_exists(self::PROPERTY_PATH, $this->items);
    }

    /**
     * Gets the path of the field value.
     *
     * @return string|null
     */
    public function getPropertyPath()
    {
        return array_key_exists(self::PROPERTY_PATH, $this->items)
            ? $this->items[self::PROPERTY_PATH]
            : null;
    }

    /**
     * Sets the path of the field value.
     *
     * @param string|null $propertyPath
     */
    public function setPropertyPath($propertyPath = null)
    {
        if ($propertyPath) {
            $this->items[self::PROPERTY_PATH] = $propertyPath;
        } else {
            unset($this->items[self::PROPERTY_PATH]);
        }
    }

    /**
     * Checks whether the field represents some metadata property like '__class__' or '__discriminator__'
     *
     * @return bool
     */
    public function isMetadataProperty()
    {
        return strpos($this->getPropertyPath(), '__') === 0;
    }

    /**
     * Gets the path of the field value.
     *
     * @return array Each item of the array can be the id of a service in DIC, an instance of
     *               "Oro\Component\EntitySerializer\DataTransformerInterface" or
     *               "Symfony\Component\Form\DataTransformerInterface",
     *               or function ($class, $property, $value, $config) : mixed.
     */
    public function getDataTransformers()
    {
        return array_key_exists(self::DATA_TRANSFORMER, $this->items)
            ? $this->items[self::DATA_TRANSFORMER]
            : [];
    }

    /**
     * Adds the data transformer to be applies to the field value.
     * The data transformer can be the id of a service in DIC or an instance of
     * "Oro\Component\EntitySerializer\DataTransformerInterface" or
     * "Symfony\Component\Form\DataTransformerInterface",
     * or function ($class, $property, $value, $config) : mixed.
     *
     * @param string|callable|DataTransformerInterface|FormDataTransformerInterface $dataTransformer
     */
    public function addDataTransformer($dataTransformer)
    {
        $transformers   = $this->getDataTransformers();
        $transformers[] = $dataTransformer;

        $this->items[self::DATA_TRANSFORMER] = $transformers;
    }
}
