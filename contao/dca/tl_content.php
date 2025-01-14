<?php

use Contao\Controller;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
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


$GLOBALS['TL_DCA']['tl_content']['fields']['headlineClass'] = [
    'label'                   => &$GLOBALS['TL_LANG']['design']['headlineClass'],
    'inputType' => 'select',
    'options' => &$GLOBALS['design']['headlineClass'],
    'reference' => &$GLOBALS['TL_LANG']['design']['headlineClass']['options'],
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
    'sql' => ['name' => 'headlineClass', 'type' => 'string', 'default' => '', 'length' => 64, 'customSchemaOptions' => ['collation' => 'ascii_bin']]
];