<?php

use Contao\Controller;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Kiwi\Contao\CmxBundle\DataContainer\PaletteManipulatorExtended;
use Kiwi\Contao\DesignerBundle\DataContainer\TemplateListener;

\Contao\System::loadLanguageFile('design');

$GLOBALS['TL_DCA']['tl_content']['fields']['customTpl']['load_callback'][] = [TemplateListener::class, 'renameTemplates'];

Controller::loadDataContainer('design');
$GLOBALS['TL_DCA']['tl_content']['fields'] += $GLOBALS['TL_DCA']['cta']['fields'];
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'isCta';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['isCta'] = 'ctaColor,ctaDesign';

PaletteManipulator::create()
    ->addField('isCta', 'template_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('hyperlink', 'tl_content');

$GLOBALS['TL_DCA']['tl_content']['fields'] += $GLOBALS['TL_DCA']['headline']['fields'];


$GLOBALS['TL_DCA']['tl_content']['fields'] += $GLOBALS['TL_DCA']['background']['fields'];

if (!$GLOBALS['responsive'] ?? true) {
    $GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'background';
    $GLOBALS['TL_DCA']['tl_content']['subpalettes']['background_color'] = "color";
    $GLOBALS['TL_DCA']['tl_content']['subpalettes']['background_picture'] = "media";
    $GLOBALS['TL_DCA']['tl_content']['subpalettes']['background_video'] = "media";
}

PaletteManipulatorExtended::create()
    ->addField('background', 'template_legend', PaletteManipulatorExtended::POSITION_APPEND)
    ->applyToPalette('element_group', 'tl_content');