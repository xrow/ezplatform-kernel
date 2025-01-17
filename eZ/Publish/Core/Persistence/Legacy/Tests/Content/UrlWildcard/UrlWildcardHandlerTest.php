<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Persistence\Legacy\Tests\Content\UrlWildcard;

use eZ\Publish\Core\Persistence\Legacy\Content\UrlWildcard\Gateway\DoctrineDatabase;
use eZ\Publish\Core\Persistence\Legacy\Content\UrlWildcard\Handler;
use eZ\Publish\Core\Persistence\Legacy\Content\UrlWildcard\Mapper;
use eZ\Publish\Core\Persistence\Legacy\Tests\TestCase;
use eZ\Publish\SPI\Persistence\Content\UrlWildcard;

/**
 * Test case for UrlWildcard Handler.
 */
class UrlWildcardHandlerTest extends TestCase
{
    /**
     * Test for the load() method.
     *
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\UrlWildcard\Handler::load
     */
    public function testLoad()
    {
        $this->insertDatabaseFixture(__DIR__ . '/Gateway/_fixtures/urlwildcards.php');
        $handler = $this->getHandler();

        $urlWildcard = $handler->load(1);

        self::assertEquals(
            new UrlWildcard(
                [
                    'id' => 1,
                    'sourceUrl' => '/developer/*',
                    'destinationUrl' => '/dev/{1}',
                    'forward' => false,
                ]
            ),
            $urlWildcard
        );
    }

    /**
     * Test for the load() method.
     *
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\UrlWildcard\Handler::load
     */
    public function testLoadThrowsNotFoundException()
    {
        $this->expectException(\eZ\Publish\API\Repository\Exceptions\NotFoundException::class);

        $this->insertDatabaseFixture(__DIR__ . '/Gateway/_fixtures/urlwildcards.php');
        $handler = $this->getHandler();

        $handler->load(100);
    }

    /**
     * Test for the create() method.
     *
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\UrlWildcard\Handler::create
     * @depends testLoad
     */
    public function testCreate()
    {
        $this->insertDatabaseFixture(__DIR__ . '/Gateway/_fixtures/urlwildcards.php');
        $handler = $this->getHandler();

        $urlWildcard = $handler->create(
            'amber',
            'pattern',
            true
        );

        self::assertEquals(
            new UrlWildcard(
                [
                    'id' => 4,
                    'sourceUrl' => '/amber',
                    'destinationUrl' => '/pattern',
                    'forward' => true,
                ]
            ),
            $urlWildcard
        );

        self::assertEquals(
            $urlWildcard,
            $handler->load(4)
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\UrlWildcard\Handler::update
     * @depends testLoad
     */
    public function testUpdate(): void
    {
        $this->insertDatabaseFixture(__DIR__ . '/Gateway/_fixtures/urlwildcards.php');
        $handler = $this->getHandler();

        $urlWildcard = $handler->load(1);

        $urlWildcardUpdated = $handler->update(
            $urlWildcard->id,
            'amber-updated',
            'pattern-updated',
            true
        );

        self::assertEquals(
            new UrlWildcard(
                [
                    'id' => 1,
                    'sourceUrl' => '/amber-updated',
                    'destinationUrl' => '/pattern-updated',
                    'forward' => true,
                ]
            ),
            $urlWildcardUpdated
        );

        self::assertEquals(
            $urlWildcardUpdated,
            $handler->load(1)
        );
    }

    /**
     * Test for the remove() method.
     *
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\UrlWildcard\Handler::remove
     * @depends testLoad
     */
    public function testRemove()
    {
        $this->expectException(\eZ\Publish\API\Repository\Exceptions\NotFoundException::class);

        $this->insertDatabaseFixture(__DIR__ . '/Gateway/_fixtures/urlwildcards.php');
        $handler = $this->getHandler();

        $handler->remove(1);
        $handler->load(1);
    }

    /**
     * Test for the loadAll() method.
     *
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\UrlWildcard\Handler::loadAll
     */
    public function testLoadAll()
    {
        $this->insertDatabaseFixture(__DIR__ . '/Gateway/_fixtures/urlwildcards.php');
        $handler = $this->getHandler();

        $urlWildcards = $handler->loadAll();

        self::assertEquals(
            [
                new UrlWildcard($this->fixtureData[0]),
                new UrlWildcard($this->fixtureData[1]),
                new UrlWildcard($this->fixtureData[2]),
            ],
            $urlWildcards
        );
    }

    /**
     * Test for the loadAll() method.
     *
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\UrlWildcard\Handler::loadAll
     */
    public function testLoadAllWithOffset()
    {
        $this->insertDatabaseFixture(__DIR__ . '/Gateway/_fixtures/urlwildcards.php');
        $handler = $this->getHandler();

        $urlWildcards = $handler->loadAll(2);

        self::assertEquals(
            [
                new UrlWildcard($this->fixtureData[2]),
            ],
            $urlWildcards
        );
    }

    /**
     * Test for the loadAll() method.
     *
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\UrlWildcard\Handler::loadAll
     */
    public function testLoadAllWithOffsetAndLimit()
    {
        $this->insertDatabaseFixture(__DIR__ . '/Gateway/_fixtures/urlwildcards.php');
        $handler = $this->getHandler();

        $urlWildcards = $handler->loadAll(1, 1);

        self::assertEquals(
            [
                new UrlWildcard($this->fixtureData[1]),
            ],
            $urlWildcards
        );
    }

    protected $fixtureData = [
        [
            'id' => 1,
            'sourceUrl' => '/developer/*',
            'destinationUrl' => '/dev/{1}',
            'forward' => false,
        ],
        [
            'id' => 2,
            'sourceUrl' => '/repository/*',
            'destinationUrl' => '/repo/{1}',
            'forward' => false,
        ],
        [
            'id' => 3,
            'sourceUrl' => '/information/*',
            'destinationUrl' => '/info/{1}',
            'forward' => false,
        ],
    ];

    /** @var \eZ\Publish\Core\Persistence\Legacy\Content\UrlWildcard\Gateway\DoctrineDatabase */
    protected $gateway;

    /** @var \eZ\Publish\Core\Persistence\Legacy\Content\UrlWildcard\Mapper */
    protected $mapper;

    /** @var \eZ\Publish\SPI\Persistence\Content\UrlWildcard\Handler */
    protected $urlWildcardHandler;

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getHandler(): UrlWildcard\Handler
    {
        if (!isset($this->urlWildcardHandler)) {
            $this->gateway = new DoctrineDatabase($this->getDatabaseConnection());
            $this->mapper = new Mapper();

            $this->urlWildcardHandler = new Handler(
                $this->gateway,
                $this->mapper
            );
        }

        return $this->urlWildcardHandler;
    }
}
