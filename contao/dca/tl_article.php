<?php

use Contao\Controller;
use Kiwi\Contao\DesignerBundle\DataContainer\TemplateListener;
use Kiwi\Contao\CmxBundle\DataContainer\PaletteManipulatorExtended;

Controller::loadDataContainer('design');

$GLOBALS['TL_DCA']['tl_article']['fields']['customTpl']['load_callback'][] = [TemplateListener::class, 'renameTemplates'];

$GLOBALS['TL_DCA']['tl_article']['fields'] += $GLOBALS['TL_DCA']['background']['fields'];

if (!$GLOBALS['responsive'] ?? true) {
    $GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'background';
    $GLOBALS['TL_DCA']['tl_content']['subpalettes']['background_color'] = "color";
    $GLOBALS['TL_DCA']['tl_content']['subpalettes']['background_picture'] = "media";
    $GLOBALS['TL_DCA']['tl_content']['subpalettes']['background_video'] = "media";
}

PaletteManipulatorExtended::create()
    ->addField('background', 'template_legend', PaletteManipulatorExtended::POSITION_APPEND)
    ->applyToPalette('default', 'tl_article');