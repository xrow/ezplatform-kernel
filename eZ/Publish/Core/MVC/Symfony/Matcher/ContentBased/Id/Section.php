<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\Id;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location as APILocation;
use eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued;
use eZ\Publish\Core\MVC\Symfony\View\ContentValueView;
use eZ\Publish\Core\MVC\Symfony\View\View;

class Section extends MultipleValued
{
    /**
     * Checks if a Location object matches.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return bool
     */
    public function matchLocation(APILocation $location)
    {
        return isset($this->values[$location->getContentInfo()->sectionId]);
    }

    /**
     * Checks if a ContentInfo object matches.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     *
     * @return bool
     */
    public function matchContentInfo(ContentInfo $contentInfo)
    {
        return isset($this->values[$contentInfo->sectionId]);
    }

    public function match(View $view)
    {
        if (!$view instanceof ContentValueView) {
            return false;
        }

        return isset($this->values[$view->getContent()->contentInfo->sectionId]);
    }
}
