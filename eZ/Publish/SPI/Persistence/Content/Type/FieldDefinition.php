<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\SPI\Persistence\Content\Type;

use eZ\Publish\SPI\Persistence\Content\FieldTypeConstraints;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\ValueObject;

/**
 * @todo Do we need a FieldDefinitionCreateStruct?
 * @todo What about the "serialized_data_text" field in legacy storage?
 */
class FieldDefinition extends ValueObject
{
    /**
     * Primary key.
     *
     * @var mixed
     */
    public $id;

    /**
     * Name.
     *
     * @var string[]
     */
    public $name;

    /**
     * Description.
     *
     * @var string[]
     */
    public $description = [];

    /**
     * Readable string identifier of a field definition.
     *
     * @var string
     */
    public $identifier;

    /**
     * Field group name.
     *
     * @var string
     */
    public $fieldGroup;

    /**
     * Position.
     *
     * @var int
     */
    public $position;

    /**
     * String identifier of the field type.
     *
     * @var string
     */
    public $fieldType;

    /**
     * If the field type is translatable.
     *
     * @var bool
     */
    public $isTranslatable;

    /**
     * Is the field required.
     *
     * @var bool
     */
    public $isRequired;

    /**
     * If the field type can be a thumbnail.
     *
     * @var bool
     */
    public $isThumbnail;

    /**
     * Just a flag.
     *
     * @var bool
     */
    public $isInfoCollector;

    /**
     * A map of field type constraints.
     * 2 constraints are available (as keys):
     *   - validators
     *   - fieldSettings.
     *
     * @var \eZ\Publish\SPI\Persistence\Content\FieldTypeConstraints
     */
    public $fieldTypeConstraints;

    /**
     * Default value of the field.
     *
     * @var \eZ\Publish\SPI\Persistence\Content\FieldValue
     */
    public $defaultValue;

    /**
     * @todo: Document
     *
     * @var bool
     */
    public $isSearchable;

    /**
     * Based on mainLanguageCode of contentType.
     *
     * @var string
     */
    public $mainLanguageCode;

    /**
     * Constructor.
     */
    public function __construct(array $properties = [])
    {
        $this->fieldTypeConstraints = new FieldTypeConstraints();
        $this->defaultValue = new FieldValue();
        parent::__construct($properties);
    }
}
