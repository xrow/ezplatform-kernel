<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\FieldType\Tests;

use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\FieldType\Selection\Type as Selection;
use eZ\Publish\Core\FieldType\Selection\Value as SelectionValue;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\SPI\FieldType\Value as SPIValue;

/**
 * @group fieldType
 * @group ezselection
 */
class SelectionTest extends FieldTypeTest
{
    /**
     * Returns the field type under test.
     *
     * This method is used by all test cases to retrieve the field type under
     * test. Just create the FieldType instance using mocks from the provided
     * get*Mock() methods and/or custom get*Mock() implementations. You MUST
     * NOT take care for test case wide caching of the field type, just return
     * a new instance from this method!
     *
     * @return FieldType
     */
    protected function createFieldTypeUnderTest()
    {
        $fieldType = new Selection();
        $fieldType->setTransformationProcessor($this->getTransformationProcessorMock());

        return $fieldType;
    }

    /**
     * Returns the validator configuration schema expected from the field type.
     *
     * @return array
     */
    protected function getValidatorConfigurationSchemaExpectation()
    {
        return [];
    }

    /**
     * Returns the settings schema expected from the field type.
     *
     * @return array
     */
    protected function getSettingsSchemaExpectation()
    {
        return [
            'isMultiple' => [
                'type' => 'bool',
                'default' => false,
            ],
            'options' => [
                'type' => 'hash',
                'default' => [],
            ],
            'multilingualOptions' => [
                'type' => 'hash',
                'default' => [],
            ],
        ];
    }

    /**
     * Returns the empty value expected from the field type.
     *
     * @return \eZ\Publish\Core\FieldType\Selection\Value
     */
    protected function getEmptyValueExpectation()
    {
        return new SelectionValue();
    }

    /**
     * Data provider for invalid input to acceptValue().
     *
     * Returns an array of data provider sets with 2 arguments: 1. The invalid
     * input to acceptValue(), 2. The expected exception type as a string. For
     * example:
     *
     * <code>
     *  return array(
     *      array(
     *          new \stdClass(),
     *          'eZ\\Publish\\Core\\Base\\Exceptions\\InvalidArgumentException',
     *      ),
     *      array(
     *          array(),
     *          'eZ\\Publish\\Core\\Base\\Exceptions\\InvalidArgumentException',
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideInvalidInputForAcceptValue()
    {
        return [
            [
                23,
                InvalidArgumentException::class,
            ],
            [
                'sindelfingen',
                InvalidArgumentException::class,
            ],
        ];
    }

    /**
     * Data provider for valid input to acceptValue().
     *
     * Returns an array of data provider sets with 2 arguments: 1. The valid
     * input to acceptValue(), 2. The expected return value from acceptValue().
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          null,
     *          null
     *      ),
     *      array(
     *          __FILE__,
     *          new BinaryFileValue( array(
     *              'path' => __FILE__,
     *              'fileName' => basename( __FILE__ ),
     *              'fileSize' => filesize( __FILE__ ),
     *              'downloadCount' => 0,
     *              'mimeType' => 'text/plain',
     *          ) )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideValidInputForAcceptValue()
    {
        return [
            [
                [],
                new SelectionValue(),
            ],
            [
                [23],
                new SelectionValue([23]),
            ],
            [
                [23, 42],
                new SelectionValue([23, 42]),
            ],
            [
                new SelectionValue([23, 42]),
                new SelectionValue([23, 42]),
            ],
        ];
    }

    /**
     * Provide input for the toHash() method.
     *
     * Returns an array of data provider sets with 2 arguments: 1. The valid
     * input to toHash(), 2. The expected return value from toHash().
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          null,
     *          null
     *      ),
     *      array(
     *          new BinaryFileValue( array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          ) ),
     *          array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideInputForToHash()
    {
        return [
            [
                new SelectionValue(),
                [],
            ],
            [
                new SelectionValue([23, 42]),
                [23, 42],
            ],
        ];
    }

    /**
     * Provide input to fromHash() method.
     *
     * Returns an array of data provider sets with 2 arguments: 1. The valid
     * input to fromHash(), 2. The expected return value from fromHash().
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          null,
     *          null
     *      ),
     *      array(
     *          array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          ),
     *          new BinaryFileValue( array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          ) )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideInputForFromHash()
    {
        return [
            [
                [],
                new SelectionValue(),
            ],
            [
                [23, 42],
                new SelectionValue([23, 42]),
            ],
        ];
    }

    /**
     * Provide data sets with field settings which are considered valid by the
     * {@link validateFieldSettings()} method.
     *
     * Returns an array of data provider sets with a single argument: A valid
     * set of field settings.
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          array(),
     *      ),
     *      array(
     *          array( 'rows' => 2 )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideValidFieldSettings()
    {
        return [
            [
                [],
            ],
            [
                [
                    'isMultiple' => true,
                    'options' => ['foo', 'bar'],
                ],
            ],
            [
                [
                    'isMultiple' => false,
                    'options' => [23, 42],
                ],
            ],
        ];
    }

    /**
     * Provide data sets with field settings which are considered invalid by the
     * {@link validateFieldSettings()} method. The method must return a
     * non-empty array of validation error when receiving such field settings.
     *
     * Returns an array of data provider sets with a single argument: A valid
     * set of field settings.
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          true,
     *      ),
     *      array(
     *          array( 'nonExistentKey' => 2 )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideInValidFieldSettings()
    {
        return [
            [
                [
                    // isMultiple must be bool
                    'isMultiple' => 23,
                ],
            ],
            [
                [
                    // options must be array
                    'options' => 23,
                ],
            ],
        ];
    }

    protected function provideFieldTypeIdentifier()
    {
        return 'ezselection';
    }

    /**
     * @dataProvider provideDataForGetName
     */
    public function testGetName(
        SPIValue $value,
        string $expected,
        array $fieldSettings = [],
        string $languageCode = 'en_GB'
    ): void {
        $fieldDefinitionMock = $this->getFieldDefinitionMock($fieldSettings);
        $fieldDefinitionMock
            ->method('__get')
            ->with('mainLanguageCode')
            ->willReturn('de_DE');

        $name = $this->getFieldTypeUnderTest()->getName($value, $fieldDefinitionMock, $languageCode);

        self::assertSame($expected, $name);
    }

    public function provideDataForGetName(): array
    {
        return [
            'empty_value_and_field_settings' => [$this->getEmptyValueExpectation(), '', [], 'en_GB'],
            'one_option' => [
                new SelectionValue(['optionIndex1']),
                'option_1',
                ['options' => ['optionIndex1' => 'option_1']],
                'en_GB',
            ],
            'two_options' => [
                new SelectionValue(['optionIndex1', 'optionIndex2']),
                'option_1 option_2',
                ['options' => ['optionIndex1' => 'option_1', 'optionIndex2' => 'option_2']],
                'en_GB',
            ],
            'multilingual_options' => [
                new SelectionValue(['optionIndex1', 'optionIndex2']),
                'option_1 option_2',
                ['multilingualOptions' => ['en_GB' => ['optionIndex1' => 'option_1', 'optionIndex2' => 'option_2']]],
                'en_GB',
            ],
            'multilingual_options_with_main_language_code' => [
                new SelectionValue(['optionIndex3', 'optionIndex4']),
                'option_3 option_4',
                ['multilingualOptions' => [
                    'en_GB' => ['optionIndex1' => 'option_1', 'optionIndex2' => 'option_2'],
                    'de_DE' => ['optionIndex3' => 'option_3', 'optionIndex4' => 'option_4'],
                ]],
                'de_DE',
            ],
        ];
    }

    /**
     * Provides data sets with validator configuration and/or field settings and
     * field value which are considered valid by the {@link validate()} method.
     *
     * ATTENTION: This is a default implementation, which must be overwritten if
     * a FieldType supports validation!
     *
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          array(
     *              "validatorConfiguration" => array(
     *                  "StringLengthValidator" => array(
     *                      "minStringLength" => 2,
     *                      "maxStringLength" => 10,
     *                  ),
     *              ),
     *          ),
     *          new TextLineValue( "lalalala" ),
     *      ),
     *      array(
     *          array(
     *              "fieldSettings" => array(
     *                  'isMultiple' => true
     *              ),
     *          ),
     *          new CountryValue(
     *              array(
     *                  "BE" => array(
     *                      "Name" => "Belgium",
     *                      "Alpha2" => "BE",
     *                      "Alpha3" => "BEL",
     *                      "IDC" => 32,
     *                  ),
     *              ),
     *          ),
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideValidDataForValidate()
    {
        return [
            [
                [
                    'fieldSettings' => [
                        'isMultiple' => true,
                        'options' => [0 => 1, 1 => 2],
                    ],
                ],
                new SelectionValue([0, 1]),
            ],
            [
                [
                    'fieldSettings' => [
                        'isMultiple' => false,
                        'options' => [0 => 1, 1 => 2],
                    ],
                ],
                new SelectionValue([1]),
            ],
            [
                [
                    'fieldSettings' => [
                        'isMultiple' => false,
                        'options' => [0 => 1, 1 => 2],
                    ],
                ],
                new SelectionValue(),
            ],
            [
                [
                    'fieldSettings' => [
                        'isMultiple' => false,
                        'options' => [0 => 1, 1 => 2],
                        'multilingualOptions' => [
                            'en_GB' => [0 => 1, 1 => 2],
                            'de_DE' => [0 => 1, 1 => 2],
                        ],
                    ],
                ],
                new SelectionValue([1]),
            ],
            [
                [
                    'fieldSettings' => [
                        'isMultiple' => false,
                        'options' => [0 => 1, 1 => 2],
                        'multilingualOptions' => [
                            'en_GB' => [0 => 1, 1 => 2],
                            'de_DE' => [0 => 1],
                        ],
                    ],
                ],
                new SelectionValue([1]),
            ],
        ];
    }

    /**
     * Provides data sets with validator configuration and/or field settings,
     * field value and corresponding validation errors returned by
     * the {@link validate()} method.
     *
     * ATTENTION: This is a default implementation, which must be overwritten
     * if a FieldType supports validation!
     *
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          array(
     *              "validatorConfiguration" => array(
     *                  "IntegerValueValidator" => array(
     *                      "minIntegerValue" => 5,
     *                      "maxIntegerValue" => 10
     *                  ),
     *              ),
     *          ),
     *          new IntegerValue( 3 ),
     *          array(
     *              new ValidationError(
     *                  "The value can not be lower than %size%.",
     *                  null,
     *                  array(
     *                      "size" => 5
     *                  ),
     *              ),
     *          ),
     *      ),
     *      array(
     *          array(
     *              "fieldSettings" => array(
     *                  "isMultiple" => false
     *              ),
     *          ),
     *          new CountryValue(
     *              "BE" => array(
     *                  "Name" => "Belgium",
     *                  "Alpha2" => "BE",
     *                  "Alpha3" => "BEL",
     *                  "IDC" => 32,
     *              ),
     *              "FR" => array(
     *                  "Name" => "France",
     *                  "Alpha2" => "FR",
     *                  "Alpha3" => "FRA",
     *                  "IDC" => 33,
     *              ),
     *          )
     *      ),
     *      array(
     *          new ValidationError(
     *              "Field definition does not allow multiple countries to be selected."
     *          ),
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideInvalidDataForValidate()
    {
        return [
            [
                [
                    'fieldSettings' => [
                        'isMultiple' => false,
                        'options' => [0 => 1, 1 => 2],
                    ],
                ],
                new SelectionValue([0, 1]),
                [
                    new ValidationError(
                        'Field definition does not allow multiple options to be selected.',
                        null,
                        [],
                        'selection'
                    ),
                ],
            ],
            [
                [
                    'fieldSettings' => [
                        'isMultiple' => false,
                        'options' => [0 => 1, 1 => 2],
                    ],
                ],
                new SelectionValue([3]),
                [
                    new ValidationError(
                        'Option with index %index% does not exist in the field definition.',
                        null,
                        [
                            '%index%' => 3,
                        ],
                        'selection'
                    ),
                ],
            ],
            [
                [
                    'fieldSettings' => [
                        'isMultiple' => false,
                        'options' => [0 => 1, 1 => 2],
                        'multilingualOptions' => [
                            'en_GB' => [0 => 1, 1 => 2],
                            'de_DE' => [0 => 1],
                        ],
                    ],
                ],
                new SelectionValue([3]),
                [
                    new ValidationError(
                        'Option with index %index% does not exist in the field definition.',
                        null,
                        [
                            '%index%' => 3,
                        ],
                        'selection'
                    ),
                ],
            ],
        ];
    }
}
