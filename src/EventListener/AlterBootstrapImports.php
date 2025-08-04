<?php

namespace Kiwi\Contao\DesignerBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;

#[AsHook('alterBootstrapImports')]
class AlterBootstrapImports
{
    public function __invoke($arrData, $strBuffer, $objLayoutListener)
    {
        $strBuffer = System::getContainer()->get('twig')->render('@KiwiDesigner/responsive/bootstrap_imports.scss.twig', $arrData);
        return $strBuffer;
    }
}
