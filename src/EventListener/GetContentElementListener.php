<?php

namespace Kiwi\Contao\DesignerBundle\EventListener;

use Contao\ContentModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;

#[AsHook('getContentElement')]
class GetContentElementListener
{
    public function __invoke(ContentModel $objContentModel, string $strBuffer, object $objElement): string
    {
        if ($objElement->Template) {
            if ($objElement->headlineClass) {
                $objElement->Template->headlineClass = $objElement->headlineClass;
            }
        }
        return $strBuffer;
    }
}