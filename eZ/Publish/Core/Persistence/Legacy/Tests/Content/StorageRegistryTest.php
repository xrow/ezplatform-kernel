<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Persistence\Legacy\Tests\Content;

use eZ\Publish\Core\FieldType\NullStorage;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageRegistry;
use eZ\Publish\Core\Persistence\Legacy\Tests\TestCase;
use eZ\Publish\SPI\FieldType\FieldStorage;

/**
 * Test case for StorageRegistry.
 */
class StorageRegistryTest extends TestCase
{
    private const TYPE_NAME = 'some-type';

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\StorageRegistry::register
     */
    public function testRegister(): void
    {
        $storage = $this->getStorageMock();
        $registry = new StorageRegistry([self::TYPE_NAME => $storage]);

        $this->assertSame($storage, $registry->getStorage(self::TYPE_NAME));
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\StorageRegistry::getStorage
     */
    public function testGetStorage()
    {
        $storage = $this->getStorageMock();
        $registry = new StorageRegistry([self::TYPE_NAME => $storage]);

        $res = $registry->getStorage(self::TYPE_NAME);

        $this->assertSame(
            $storage,
            $res
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\StorageRegistry::getStorage
     * @covers \eZ\Publish\Core\Persistence\Legacy\Exception\StorageNotFound
     */
    public function testGetNotFound()
    {
        $registry = new StorageRegistry([]);
        self::assertInstanceOf(
            NullStorage::class,
            $registry->getStorage('not-found')
        );
    }

    /**
     * Returns a mock for Storage.
     *
     * @return Storage
     */
    protected function getStorageMock()
    {
        return $this->createMock(FieldStorage::class);
    }
}
