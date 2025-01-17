<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Event\Tests;

use eZ\Publish\API\Repository\Events\URLWildcard\BeforeCreateEvent;
use eZ\Publish\API\Repository\Events\URLWildcard\BeforeRemoveEvent;
use eZ\Publish\API\Repository\Events\URLWildcard\BeforeTranslateEvent;
use eZ\Publish\API\Repository\Events\URLWildcard\BeforeUpdateEvent;
use eZ\Publish\API\Repository\Events\URLWildcard\CreateEvent;
use eZ\Publish\API\Repository\Events\URLWildcard\RemoveEvent;
use eZ\Publish\API\Repository\Events\URLWildcard\TranslateEvent;
use eZ\Publish\API\Repository\Events\URLWildcard\UpdateEvent;
use eZ\Publish\API\Repository\URLWildcardService as URLWildcardServiceInterface;
use eZ\Publish\API\Repository\Values\Content\URLWildcard;
use eZ\Publish\API\Repository\Values\Content\URLWildcardTranslationResult;
use eZ\Publish\API\Repository\Values\Content\URLWildcardUpdateStruct;
use eZ\Publish\Core\Event\URLWildcardService;

class URLWildcardServiceTest extends AbstractServiceTest
{
    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testRemoveEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeRemoveEvent::class,
            RemoveEvent::class
        );

        $parameters = [
            $this->createMock(URLWildcard::class),
        ];

        $innerServiceMock = $this->createMock(URLWildcardServiceInterface::class);

        $service = new URLWildcardService($innerServiceMock, $traceableEventDispatcher);
        $service->remove(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeRemoveEvent::class, 0],
            [RemoveEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testRemoveStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeRemoveEvent::class,
            RemoveEvent::class
        );

        $parameters = [
            $this->createMock(URLWildcard::class),
        ];

        $innerServiceMock = $this->createMock(URLWildcardServiceInterface::class);

        $traceableEventDispatcher->addListener(BeforeRemoveEvent::class, static function (BeforeRemoveEvent $event) {
            $event->stopPropagation();
        }, 10);

        $service = new URLWildcardService($innerServiceMock, $traceableEventDispatcher);
        $service->remove(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($calledListeners, [
            [BeforeRemoveEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeRemoveEvent::class, 0],
            [RemoveEvent::class, 0],
        ]);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testUpdateEvents(): void
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdateEvent::class,
            UpdateEvent::class
        );

        $innerServiceMock = $this->createMock(URLWildcardServiceInterface::class);

        $service = new URLWildcardService($innerServiceMock, $traceableEventDispatcher);
        $service->update(
            $this->createMock(URLWildcard::class),
            new URLWildcardUpdateStruct()
        );

        $calledListeners = $this->getListenersStack(
            $traceableEventDispatcher->getCalledListeners()
        );

        $this->assertSame($calledListeners, [
            [BeforeUpdateEvent::class, 0],
            [UpdateEvent::class, 0],
        ]);

        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testUpdateStopPropagationInBeforeEvents(): void
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeUpdateEvent::class,
            UpdateEvent::class
        );

        $innerServiceMock = $this->createMock(URLWildcardServiceInterface::class);

        $traceableEventDispatcher->addListener(
            BeforeUpdateEvent::class,
            static function (BeforeUpdateEvent $event) {
                $event->stopPropagation();
            },
            10
        );

        $service = new URLWildcardService($innerServiceMock, $traceableEventDispatcher);
        $service->update(
            $this->createMock(URLWildcard::class),
            new URLWildcardUpdateStruct()
        );

        $calledListeners = $this->getListenersStack(
            $traceableEventDispatcher->getCalledListeners()
        );
        $notCalledListeners = $this->getListenersStack(
            $traceableEventDispatcher->getNotCalledListeners()
        );

        $this->assertSame($calledListeners, [
            [BeforeUpdateEvent::class, 10],
        ]);

        $this->assertSame($notCalledListeners, [
            [BeforeUpdateEvent::class, 0],
            [UpdateEvent::class, 0],
        ]);
    }

    public function testCreateEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeCreateEvent::class,
            CreateEvent::class
        );

        $parameters = [
            'random_value_5cff79c316c1f5.58580131',
            'random_value_5cff79c316c223.93334332',
            'random_value_5cff79c316c237.08397355',
        ];

        $urlWildcard = $this->createMock(URLWildcard::class);
        $innerServiceMock = $this->createMock(URLWildcardServiceInterface::class);
        $innerServiceMock->method('create')->willReturn($urlWildcard);

        $service = new URLWildcardService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->create(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($urlWildcard, $result);
        $this->assertSame($calledListeners, [
            [BeforeCreateEvent::class, 0],
            [CreateEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnCreateResultInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeCreateEvent::class,
            CreateEvent::class
        );

        $parameters = [
            'random_value_5cff79c316c2d5.26653678',
            'random_value_5cff79c316c2e7.55400833',
            'random_value_5cff79c316c2f8.59874187',
        ];

        $urlWildcard = $this->createMock(URLWildcard::class);
        $eventUrlWildcard = $this->createMock(URLWildcard::class);
        $innerServiceMock = $this->createMock(URLWildcardServiceInterface::class);
        $innerServiceMock->method('create')->willReturn($urlWildcard);

        $traceableEventDispatcher->addListener(BeforeCreateEvent::class, static function (BeforeCreateEvent $event) use ($eventUrlWildcard) {
            $event->setUrlWildcard($eventUrlWildcard);
        }, 10);

        $service = new URLWildcardService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->create(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($eventUrlWildcard, $result);
        $this->assertSame($calledListeners, [
            [BeforeCreateEvent::class, 10],
            [BeforeCreateEvent::class, 0],
            [CreateEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testCreateStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeCreateEvent::class,
            CreateEvent::class
        );

        $parameters = [
            'random_value_5cff79c316c359.46056769',
            'random_value_5cff79c316c361.53134429',
            'random_value_5cff79c316c374.82657815',
        ];

        $urlWildcard = $this->createMock(URLWildcard::class);
        $eventUrlWildcard = $this->createMock(URLWildcard::class);
        $innerServiceMock = $this->createMock(URLWildcardServiceInterface::class);
        $innerServiceMock->method('create')->willReturn($urlWildcard);

        $traceableEventDispatcher->addListener(BeforeCreateEvent::class, static function (BeforeCreateEvent $event) use ($eventUrlWildcard) {
            $event->setUrlWildcard($eventUrlWildcard);
            $event->stopPropagation();
        }, 10);

        $service = new URLWildcardService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->create(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($eventUrlWildcard, $result);
        $this->assertSame($calledListeners, [
            [BeforeCreateEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeCreateEvent::class, 0],
            [CreateEvent::class, 0],
        ]);
    }

    public function testTranslateEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeTranslateEvent::class,
            TranslateEvent::class
        );

        $parameters = [
            'random_value_5cff79c316cfa7.72466150',
        ];

        $result = $this->createMock(URLWildcardTranslationResult::class);
        $innerServiceMock = $this->createMock(URLWildcardServiceInterface::class);
        $innerServiceMock->method('translate')->willReturn($result);

        $service = new URLWildcardService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->translate(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($result, $result);
        $this->assertSame($calledListeners, [
            [BeforeTranslateEvent::class, 0],
            [TranslateEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testReturnTranslateResultInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeTranslateEvent::class,
            TranslateEvent::class
        );

        $parameters = [
            'random_value_5cff79c316d370.25863709',
        ];

        $result = $this->createMock(URLWildcardTranslationResult::class);
        $eventResult = $this->createMock(URLWildcardTranslationResult::class);
        $innerServiceMock = $this->createMock(URLWildcardServiceInterface::class);
        $innerServiceMock->method('translate')->willReturn($result);

        $traceableEventDispatcher->addListener(BeforeTranslateEvent::class, static function (BeforeTranslateEvent $event) use ($eventResult) {
            $event->setResult($eventResult);
        }, 10);

        $service = new URLWildcardService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->translate(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($eventResult, $result);
        $this->assertSame($calledListeners, [
            [BeforeTranslateEvent::class, 10],
            [BeforeTranslateEvent::class, 0],
            [TranslateEvent::class, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    public function testTranslateStopPropagationInBeforeEvents()
    {
        $traceableEventDispatcher = $this->getEventDispatcher(
            BeforeTranslateEvent::class,
            TranslateEvent::class
        );

        $parameters = [
            'random_value_5cff79c316d3f9.73226122',
        ];

        $result = $this->createMock(URLWildcardTranslationResult::class);
        $eventResult = $this->createMock(URLWildcardTranslationResult::class);
        $innerServiceMock = $this->createMock(URLWildcardServiceInterface::class);
        $innerServiceMock->method('translate')->willReturn($result);

        $traceableEventDispatcher->addListener(BeforeTranslateEvent::class, static function (BeforeTranslateEvent $event) use ($eventResult) {
            $event->setResult($eventResult);
            $event->stopPropagation();
        }, 10);

        $service = new URLWildcardService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->translate(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($eventResult, $result);
        $this->assertSame($calledListeners, [
            [BeforeTranslateEvent::class, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [BeforeTranslateEvent::class, 0],
            [TranslateEvent::class, 0],
        ]);
    }
}
