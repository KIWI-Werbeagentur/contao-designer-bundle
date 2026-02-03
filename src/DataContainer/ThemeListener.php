<?php

namespace Kiwi\Contao\DesignerBundle\DataContainer;

use Contao\DataContainer;
use Contao\System;

class ThemeListener
{
    public function initScssFile(?DataContainer $objDca = null, ?int $undoId = null): void
    {
        $targetPath = System::getContainer()->getParameter('kernel.project_dir') . '/files/themes/';

        if (!file_exists($targetPath . '_colorvars.scss')) {
            $colorListener = new ColorListener();
            $colorListener->updateScssFile($objDca);
        }
    }
}