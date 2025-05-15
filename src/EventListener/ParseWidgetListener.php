<?php

namespace Kiwi\Contao\DesignerBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\System;

#[AsHook('parseWidget')]
class ParseWidgetListener
{
    public function __invoke($strBuffer, $objWidget)
    {
        try {
            $objWidget->inputClasses .= System::getContainer()->get('kiwi.contao.designer.frontend')->getCtaClasses($objWidget);
        }
        catch(\Exception $e){}
        return $objWidget->inherit();
    }
}