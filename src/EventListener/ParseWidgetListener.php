<?php

namespace Kiwi\Contao\DesignerBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\System;

#[AsHook('parseWidget')]
class ParseWidgetListener
{
    public function __invoke($strBuffer, $objWidget)
    {
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();
        if(!System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request)) {
            try {
                $objWidget->inputClasses .= System::getContainer()->get('kiwi.contao.designer.frontend')->getCtaClasses($objWidget);
            } catch (\Exception $e) {
            }
            $objWidget->strName = str_replace("[]", "", $objWidget->strName);
            return $objWidget->inherit();
        }
        return $strBuffer;
    }
}