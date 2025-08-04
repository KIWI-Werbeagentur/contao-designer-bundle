<?php

namespace Kiwi\Contao\DesignerBundle\EventListener;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Database;
use Contao\StringUtil;
use Contao\System;
use Kiwi\Contao\CmxBundle\DataContainer\PaletteManipulatorExtended;

#[AsHook('loadDataContainer')]
class LoadDataContainerListener
{
    public function __invoke(string $strTable): void
    {
        foreach ($GLOBALS['TL_DCA'][$strTable]['palettes'] ?? [] as $strPalette => $strFields) {
            if ($strPalette !== '__selector__' && PaletteManipulatorExtended::create()->hasField($strPalette, $strTable, 'headline')) {
                PaletteManipulator::create()
                    ->addField(['topline','subline'], 'headline')
                    ->applyToPalette($strPalette, $strTable);
                PaletteManipulator::create()
                    ->addField('headlineClass', 'headline')
                    ->applyToPalette($strPalette, $strTable);
            }
        }

        if ($strTable == 'tl_content') {
            PaletteManipulatorExtended::create()
                ->addField(['backgroundOverwrite', 'background', 'scheme'], 'template_legend', PaletteManipulatorExtended::POSITION_APPEND)
                ->applyToPalettes($GLOBALS['design']['tl_content']['background'], 'tl_content');
        }

        if($strTable == 'tl_layout') {
            $GLOBALS['TL_DCA']['tl_layout']['fields']['framework']['default'] = array_unique(array_merge(["color_styles"],$GLOBALS['TL_DCA']['tl_layout']['fields']['framework']['default'] ?? []));
            $strDefaults = serialize($GLOBALS['TL_DCA']['tl_layout']['fields']['framework']['default']);
            $GLOBALS['TL_DCA']['tl_layout']['fields']['framework']['sql'] = "varchar(255) NOT NULL default '$strDefaults'";
        }
    }
}