<?php

namespace Kiwi\Contao\DesignerBundle\EventListener;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Kiwi\Contao\CmxBundle\DataContainer\PaletteManipulatorExtended;

#[AsHook('loadDataContainer')]
class LoadDataContainerListener
{
    public function __invoke(string $strTable): void
    {
        foreach ($GLOBALS['TL_DCA'][$strTable]['palettes'] ?? [] as $strPalette => $strFields) {
            if ($strPalette !== '__selector__' && PaletteManipulatorExtended::create()->hasField($strPalette, $strTable, 'headline')) {
                PaletteManipulator::create()
                    ->addField('headlineClass', 'headline')
                    ->applyToPalette($strPalette, $strTable);
            }
        }
    }
}