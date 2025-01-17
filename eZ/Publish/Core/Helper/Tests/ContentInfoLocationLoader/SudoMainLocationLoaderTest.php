<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Helper\Tests\ContentInfoLocationLoader;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Helper\ContentInfoLocationLoader\SudoMainLocationLoader;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\Repository\Mapper\RoleDomainMapper;
use eZ\Publish\Core\Repository\Permission\LimitationService;
use eZ\Publish\Core\Repository\Permission\PermissionResolver;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\SPI\Persistence\User\Handler as SPIUserHandler;
use PHPUnit\Framework\TestCase;

class SudoMainLocationLoaderTest extends TestCase
{
    /** @var \eZ\Publish\Core\Helper\ContentInfoLocationLoader\SudoMainLocationLoader */
    private $loader;

    protected function setUp(): void
    {
        $this->loader = new SudoMainLocationLoader($this->getRepositoryMock());
    }

    public function testLoadLocationNoMainLocation()
    {
        $this->expectException(\eZ\Publish\API\Repository\Exceptions\NotFoundException::class);

        $contentInfo = new ContentInfo();

        $this->getLocationServiceMock()
            ->expects($this->never())
            ->method('loadLocation');

        $this->loader->loadLocation($contentInfo);
    }

    public function testLoadLocation()
    {
        $contentInfo = new ContentInfo(['mainLocationId' => 42]);
        $location = new Location();

        $this->getRepositoryMock()
            ->expects($this->any())
            ->method('getPermissionResolver')
            ->will($this->returnValue($this->getPermissionResolverMock()));

        $this->getRepositoryMock()
            ->expects($this->any())
            ->method('getLocationService')
            ->will($this->returnValue($this->getLocationServiceMock()));

        $this->getLocationServiceMock()
            ->expects($this->once())
            ->method('loadLocation')
            ->with(42)
            ->will($this->returnValue($location));

        $this->assertSame($location, $this->loader->loadLocation($contentInfo));
    }

    public function testLoadLocationError()
    {
        $this->expectException(\eZ\Publish\API\Repository\Exceptions\NotFoundException::class);

        $contentInfo = new ContentInfo(['mainLocationId' => 42]);
        $location = new Location();

        $this->getRepositoryMock()
            ->expects($this->any())
            ->method('getPermissionResolver')
            ->will($this->returnValue($this->getPermissionResolverMock()));

        $this->getRepositoryMock()
            ->expects($this->any())
            ->method('getLocationService')
            ->will($this->returnValue($this->getLocationServiceMock()));

        $this->getLocationServiceMock()
            ->expects($this->once())
            ->method('loadLocation')
            ->with(42)
            ->will(
                $this->throwException(new NotFoundException('main location of content', 42))
            );

        $this->assertSame($location, $this->loader->loadLocation($contentInfo));
    }

    /**
     * @return \eZ\Publish\Core\Repository\Repository|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getRepositoryMock()
    {
        static $repositoryMock;

        if ($repositoryMock === null) {
            $repositoryClass = Repository::class;

            $repositoryMock = $this
                ->getMockBuilder($repositoryClass)
                ->disableOriginalConstructor()
                ->setMethods(
                    array_diff(
                        get_class_methods($repositoryClass),
                        ['sudo']
                    )
                )
                ->getMock();
        }

        return $repositoryMock;
    }

    /**
     * @return \eZ\Publish\API\Repository\LocationService|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getLocationServiceMock()
    {
        static $mock;

        if ($mock === null) {
            $mock = $this
                ->getMockBuilder(LocationService::class)
                ->getMock();
        }

        return $mock;
    }

    /**
     * @return \eZ\Publish\Core\Repository\Permission\PermissionResolver|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getPermissionResolverMock()
    {
        $configResolverMock = $this->createMock(ConfigResolverInterface::class);
        $configResolverMock
            ->method('getParameter')
            ->with('anonymous_user_id')
            ->willReturn(10);

        return $this
            ->getMockBuilder(PermissionResolver::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    $this
                        ->getMockBuilder(RoleDomainMapper::class)
                        ->disableOriginalConstructor()
                        ->getMock(),
                    $this
                        ->getMockBuilder(LimitationService::class)
                        ->getMock(),
                    $this
                        ->getMockBuilder(SPIUserHandler::class)
                        ->getMock(),
                    $configResolverMock,
                    [],
                ]
            )
            ->getMock();
    }
}
