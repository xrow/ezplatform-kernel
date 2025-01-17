<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\SPI\Tests\FieldType;

use eZ\Publish\API\Repository\PasswordHashService;
use eZ\Publish\Core\FieldType;
use eZ\Publish\Core\Persistence\Legacy;
use eZ\Publish\Core\Repository\User\PasswordValidatorInterface;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\FieldTypeConstraints;
use eZ\Publish\SPI\Persistence\User;

/**
 * Integration test for legacy storage field types.
 *
 * This abstract base test case is supposed to be the base for field type
 * integration tests. It basically calls all involved methods in the field type
 * ``Converter`` and ``Storage`` implementations. Fo get it working implement
 * the abstract methods in a sensible way.
 *
 * The following actions are performed by this test using the custom field
 * type:
 *
 * - Create a new content type with the given field type
 * - Load create content type
 * - Create content object of new content type
 * - Load created content
 * - Copy created content
 * - Remove copied content
 *
 * @group integration
 */
class UserIntegrationTest extends BaseIntegrationTest
{
    /**
     * Get name of tested field type.
     *
     * @return string
     */
    public function getTypeName()
    {
        return 'ezuser';
    }

    /**
     * Get handler with required custom field types registered.
     *
     * @return \eZ\Publish\SPI\Persistence\Handler
     */
    public function getCustomHandler()
    {
        $userHandler = $this->createMock(User\Handler::class);
        $passwordHashGenerator = $this->createMock(PasswordHashService::class);
        $passwordHashGenerator
            ->method('getDefaultHashType')
            ->willReturn(0);
        $passwordValidator = $this->createMock(PasswordValidatorInterface::class);

        $fieldType = new FieldType\User\Type(
            $userHandler,
            $passwordHashGenerator,
            $passwordValidator
        );
        $fieldType->setTransformationProcessor($this->getTransformationProcessor());

        return $this->getHandler(
            'ezuser',
            $fieldType,
            new Legacy\Content\FieldValue\Converter\NullConverter(),
            new FieldType\User\UserStorage(
                new FieldType\User\UserStorage\Gateway\DoctrineStorage(
                    $this->getDatabaseConnection()
                )
            )
        );
    }

    /**
     * Returns the FieldTypeConstraints to be used to create a field definition
     * of the FieldType under test.
     *
     * @return \eZ\Publish\SPI\Persistence\Content\FieldTypeConstraints
     */
    public function getTypeConstraints()
    {
        return new FieldTypeConstraints();
    }

    /**
     * Get field definition data values.
     *
     * This is a PHPUnit data provider
     *
     * @return array
     */
    public function getFieldDefinitionData()
    {
        return [
            // The user field type does not have any special field definition
            // properties
            ['fieldType', 'ezuser'],
            ['fieldTypeConstraints', new Content\FieldTypeConstraints()],
        ];
    }

    /**
     * Get initial field value.
     *
     * @return \eZ\Publish\SPI\Persistence\Content\FieldValue
     */
    public function getInitialValue()
    {
        return new Content\FieldValue(
            [
                'data' => null,
                'externalData' => [
                    'login' => 'hans',
                    'email' => 'hans@example.com',
                    'passwordHash' => '*',
                    'passwordHashType' => 0,
                    'enabled' => true,
                    'maxLogin' => 1000,
                    'passwordUpdatedAt' => 1569229200,
                ],
                'sortKey' => 'user',
            ]
        );
    }

    /**
     * Asserts that the loaded field data is correct.
     *
     * Performs assertions on the loaded field, mainly checking that the
     * $field->value->externalData is loaded correctly. If the loading of
     * external data manipulates other aspects of $field, their correctness
     * also needs to be asserted. Make sure you implement this method agnostic
     * to the used SPI\Persistence implementation!
     */
    public function assertLoadedFieldDataCorrect(Field $field)
    {
        $expectedValues = [
            'hasStoredLogin' => true,
            'contentId' => self::$contentId,
            'login' => 'hans',
            'email' => 'hans@example.com',
            'passwordHash' => '*',
            'passwordHashType' => 0,
            'enabled' => true,
            'maxLogin' => 1000,
            'passwordUpdatedAt' => 1569229200,
        ];

        foreach ($expectedValues as $key => $value) {
            $this->assertEquals($value, $field->value->externalData[$key]);
        }
    }

    /**
     * Get update field value.
     *
     * Use to update the field
     *
     * @return \eZ\Publish\SPI\Persistence\Content\FieldValue
     */
    public function getUpdatedValue()
    {
        return new Content\FieldValue(
            [
                'data' => null,
                'externalData' => [
                    'login' => 'changeLogin',
                    'email' => 'changeEmail@ez.no',
                    'passwordHash' => '*2',
                    'passwordHashType' => 1,
                    'enabled' => false,
                    'maxLogin' => 1,
                    'passwordUpdatedAt' => 1569229299,
                ],
                'sortKey' => 'user',
            ]
        );
    }

    /**
     * Asserts that the updated field data is loaded correct.
     *
     * Performs assertions on the loaded field after it has been updated,
     * mainly checking that the $field->value->externalData is loaded
     * correctly. If the loading of external data manipulates other aspects of
     * $field, their correctness also needs to be asserted. Make sure you
     * implement this method agnostic to the used SPI\Persistence
     * implementation!
     */
    public function assertUpdatedFieldDataCorrect(Field $field)
    {
        $expectedValues = [
            'hasStoredLogin' => true,
            'contentId' => self::$contentId,
            'login' => 'changeLogin',
            'email' => 'changeEmail@ez.no',
            'passwordHash' => '*2',
            'passwordHashType' => 1,
            'enabled' => false,
            'maxLogin' => 1,
            'passwordUpdatedAt' => 1569229299,
        ];

        foreach ($expectedValues as $key => $value) {
            $this->assertEquals($value, $field->value->externalData[$key]);
        }
    }
}
