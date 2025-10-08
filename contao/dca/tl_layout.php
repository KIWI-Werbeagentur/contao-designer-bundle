<?php

use Kiwi\Contao\CmxBundle\DataContainer\PaletteManipulatorExtended;
use Kiwi\Contao\DesignerBundle\DataContainer\ColorSchemeListener;

$GLOBALS['TL_DCA']['tl_layout']['fields']['scheme'] = [
    'inputType' => 'select',
    'foreignKey' => 'tl_color_scheme.title',
    'eval' => ['tl_class' => 'clr w50'],
    'sql' => "int(10) unsigned NOT NULL default 0",
    'relation' => ['type' => 'hasOne', 'load' => 'lazy']
];

PaletteManipulatorExtended::create()
    ->addField('scheme', 'style_legend', PaletteManipulatorExtended::POSITION_APPEND)
    ->applyToPalette('default', 'tl_layout');

$GLOBALS['TL_DCA']['tl_layout']['config']['onsubmit_callback'][] =  [ColorSchemeListener::class, 'generateSchemesScss'];
$GLOBALS['TL_DCA']['tl_layout']['config']['ondelete_callback'][] =  [ColorSchemeListener::class, 'generateSchemesScss'];

$GLOBALS['TL_DCA']['tl_layout']['fields']['framework']['options'][] = 'background_styles';
$GLOBALS['TL_DCA']['tl_layout']['fields']['framework']['options'][] = 'color_styles';
