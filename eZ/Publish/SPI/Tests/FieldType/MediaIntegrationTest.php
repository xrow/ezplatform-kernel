<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\SPI\Tests\FieldType;

use eZ\Publish\Core\FieldType;
use eZ\Publish\Core\IO\MimeTypeDetector\FileInfo;
use eZ\Publish\Core\Persistence\Legacy;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\FieldTypeConstraints;
use FileSystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

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
class MediaIntegrationTest extends FileBaseIntegrationTest
{
    /**
     * Returns the storage identifier prefix used by the file service.
     *
     * @return string
     */
    protected function getStoragePrefix()
    {
        return self::$container->getParameter('binaryfile_storage_prefix');
    }

    /**
     * Get name of tested field type.
     *
     * @return string
     */
    public function getTypeName()
    {
        return 'ezmedia';
    }

    /**
     * Get handler with required custom field types registered.
     *
     * @return \eZ\Publish\SPI\Persistence\Handler
     */
    public function getCustomHandler()
    {
        $fieldType = new FieldType\Media\Type([
            self::$container->get('ezpublish.fieldType.validator.black_list'),
        ]);
        $fieldType->setTransformationProcessor($this->getTransformationProcessor());

        return $this->getHandler(
            'ezmedia',
            $fieldType,
            new Legacy\Content\FieldValue\Converter\MediaConverter(),
            new FieldType\Media\MediaStorage(
                new FieldType\Media\MediaStorage\Gateway\DoctrineStorage($this->getDatabaseConnection()),
                $this->ioService = self::$container->get('ezpublish.fieldType.ezbinaryfile.io_service'),
                $legacyPathGenerator = new FieldType\BinaryBase\PathGenerator\LegacyPathGenerator(),
                new FileInfo()
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
        return new FieldTypeConstraints(
            [
                'validators' => [
                    'FileSizeValidator' => [
                        'maxFileSize' => 2 * 1024 * 1024, // 2 MB
                    ],
                ],
                'fieldSettings' => new FieldType\FieldSettings(
                    [
                        'mediaType' => FieldType\Media\Type::TYPE_SILVERLIGHT,
                    ]
                ),
            ]
        );
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
            ['fieldType', 'ezmedia'],
            [
                'fieldTypeConstraints',
                new FieldTypeConstraints(
                    [
                        'validators' => [
                            'FileSizeValidator' => [
                                'maxFileSize' => 2 * 1024 * 1024, // 2 MB
                            ],
                        ],
                        'fieldSettings' => new FieldType\FieldSettings(
                            [
                                'mediaType' => FieldType\Media\Type::TYPE_SILVERLIGHT,
                            ]
                        ),
                    ]
                ),
            ],
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
                    'id' => null,
                    'inputUri' => ($path = __DIR__ . '/_fixtures/image.jpg'),
                    'fileName' => 'Ice-Flower-Media.jpg',
                    'fileSize' => filesize($path),
                    'mimeType' => 'image/jpeg',
                    'hasController' => true,
                    'autoplay' => true,
                    'loop' => true,
                    'width' => 23,
                    'height' => 42,
                    'uri' => $path,
                ],
                'sortKey' => '',
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
        $this->assertNotNull($field->value->externalData);

        $this->assertFileExists(
            $path = $this->getStorageDir() . '/' . $this->getStoragePrefix() . '/' . $field->value->externalData['id'],
            "Stored file $path does not exists"
        );

        $this->assertEquals('Ice-Flower-Media.jpg', $field->value->externalData['fileName']);
        $this->assertEquals(filesize($path), $field->value->externalData['fileSize']);
        $this->assertEquals('image/jpeg', $field->value->externalData['mimeType']);
        $this->assertTrue($field->value->externalData['hasController']);
        $this->assertTrue($field->value->externalData['autoplay']);
        $this->assertTrue($field->value->externalData['loop']);
        $this->assertEquals(23, $field->value->externalData['width']);
        $this->assertEquals(42, $field->value->externalData['height']);

        $this->assertNull($field->value->data);
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
                    'id' => null,
                    'inputUri' => ($path = __DIR__ . '/_fixtures/image.png'),
                    'fileName' => 'Blueish-Blue-Media.jpg',
                    'fileSize' => filesize($path),
                    'mimeType' => 'image/png',
                    'hasController' => false,
                    'autoplay' => false,
                    'loop' => false,
                    'width' => 0,
                    'height' => 0,
                    'uri' => $path,
                ],
                'sortKey' => '',
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
        $this->assertNotNull($field->value->externalData);

        $this->assertFileExists(
            $path = $this->getStorageDir() . '/' . $this->getStoragePrefix() . '/' . $field->value->externalData['id'],
            "Stored file $path does not exists"
        );

        // Check old file removed before update
        $this->assertCount(
            1,
            glob(dirname($path) . '/*')
        );

        $this->assertEquals('Blueish-Blue-Media.jpg', $field->value->externalData['fileName']);
        $this->assertEquals(filesize($path), $field->value->externalData['fileSize']);
        $this->assertEquals('image/png', $field->value->externalData['mimeType']);
        $this->assertFalse($field->value->externalData['hasController']);
        $this->assertFalse($field->value->externalData['autoplay']);
        $this->assertFalse($field->value->externalData['loop']);
        $this->assertEquals(0, $field->value->externalData['width']);
        $this->assertEquals(0, $field->value->externalData['height']);

        $this->assertNull($field->value->data);
    }

    /**
     * Can be overwritten to assert that additional data has been deleted.
     *
     * @param \eZ\Publish\SPI\Persistence\Content $content
     */
    public function assertDeletedFieldDataCorrect(Content $content)
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $this->getStorageDir(),
                FileSystemIterator::KEY_AS_PATHNAME | FileSystemIterator::SKIP_DOTS | FileSystemIterator::CURRENT_AS_FILEINFO
            ),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $path => $fileInfo) {
            if ($fileInfo->isFile()) {
                $this->fail(
                    sprintf(
                        'Found undeleted file "%s"',
                        $path
                    )
                );
            }
        }
    }
}
