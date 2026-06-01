<?php

use Contao\Controller;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\System;
use Kiwi\Contao\DesignerBundle\DataContainer\CtaListener;
use Kiwi\Contao\DesignerBundle\DataContainer\TemplateListener;

System::loadLanguageFile('design');

$GLOBALS['TL_DCA']['tl_content']['fields']['customTpl']['load_callback'][] = [TemplateListener::class, 'renameTemplates'];

Controller::loadDataContainer('design');
$GLOBALS['TL_DCA']['tl_content']['fields'] += $GLOBALS['TL_DCA']['cta']['fields'];
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'isCta';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['isCta'] = 'ctaColor,ctaDesign';
$GLOBALS['TL_DCA']['tl_content']['config']['onbeforesubmit_callback'][] = [CtaListener::class, 'beforeSubmitCallback'];
$GLOBALS['TL_DCA']['tl_content']['fields']['isCta']['load_callback'][] = [CtaListener::class, 'loadCallback'];


$ctaField = PaletteManipulator::create()
    ->addField('isCta', 'template_legend', PaletteManipulator::POSITION_APPEND);

foreach (['hyperlink', 'download', 'downloads'] as $palette) {
    $ctaField->applyToPalette($palette, 'tl_content');
}


$GLOBALS['TL_DCA']['tl_content']['fields'] += $GLOBALS['TL_DCA']['headline']['fields'];


$GLOBALS['TL_DCA']['tl_content']['fields'] += $GLOBALS['TL_DCA']['background']['fields'];

if (!$GLOBALS['responsive'] ?? true) {
    $GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'background';
    $GLOBALS['TL_DCA']['tl_content']['subpalettes']['background_color'] = "color";
    $GLOBALS['TL_DCA']['tl_content']['subpalettes']['background_picture'] = "media";
    $GLOBALS['TL_DCA']['tl_content']['subpalettes']['background_video'] = "media";
}

$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'backgroundOverwrite';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['backgroundOverwrite'] = "overwriteTable,overwriteField,overwriteParameter";