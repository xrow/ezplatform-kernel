<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\SPI\Persistence\Filter\Location;

use eZ\Publish\SPI\Persistence\Filter\LazyListIterator;

/**
 * SPI Persistence Content Item list iterator.
 *
 * @internal for internal use by Repository Filtering
 *
 * @see \eZ\Publish\SPI\Persistence\Content\ContentItem
 */
class LazyLocationListIterator extends LazyListIterator
{
    /**
     * @return \eZ\Publish\API\Repository\Values\Content\LocationList[]
     *
     * @throws \Exception
     */
    #[\ReturnTypeWillChange]
    public function getIterator(): iterable
    {
        yield from parent::getIterator();
    }
}
