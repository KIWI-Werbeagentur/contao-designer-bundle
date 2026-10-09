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
    ->applyToPalette('hyperlink', 'tl_content')
    ->applyToPalette('download', 'tl_content')
    ->applyToPalette('downloads', 'tl_content');

$GLOBALS['TL_DCA']['tl_content']['fields'] += $GLOBALS['TL_DCA']['headline']['fields'];

// Non-semantic headlines (e.g. a visual title that must not affect the document outline)
foreach (['headline', 'sectionHeadline'] as $strField) {
    if (\is_array($GLOBALS['TL_DCA']['tl_content']['fields'][$strField]['options'] ?? null)
        && !\in_array('div', $GLOBALS['TL_DCA']['tl_content']['fields'][$strField]['options'], true)
    ) {
        array_unshift($GLOBALS['TL_DCA']['tl_content']['fields'][$strField]['options'], 'div');
    }
}

// Section headlines (accordion, tabs, …) get the same visual class select as regular headlines.
// The field is placed into the palettes by SectionHeadlineListener, because sectionHeadline itself
// is only added by onpalette callbacks.
$GLOBALS['TL_DCA']['tl_content']['fields']['sectionHeadlineClass'] = array_merge(
    $GLOBALS['TL_DCA']['headline']['fields']['headlineClass'],
    [
        'label' => &$GLOBALS['TL_LANG']['design']['sectionHeadlineClass'],
        'sql' => ['name' => 'sectionHeadlineClass', 'type' => 'string', 'default' => '', 'length' => 64, 'customSchemaOptions' => ['collation' => 'ascii_bin']],
    ]
);


$GLOBALS['TL_DCA']['tl_content']['fields'] += $GLOBALS['TL_DCA']['background']['fields'];

if (!$GLOBALS['responsive'] ?? true) {
    $GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'background';
    $GLOBALS['TL_DCA']['tl_content']['subpalettes']['background_color'] = "color";
    $GLOBALS['TL_DCA']['tl_content']['subpalettes']['background_picture'] = "media";
    $GLOBALS['TL_DCA']['tl_content']['subpalettes']['background_video'] = "media";
}

$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'backgroundOverwrite';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['backgroundOverwrite'] = "overwriteTable,overwriteField,overwriteParameter";
