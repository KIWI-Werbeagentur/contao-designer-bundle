<?php

use Contao\Controller;
use Contao\CoreBundle\DataContainer\PaletteManipulator;

Controller::loadDataContainer('design');
$GLOBALS['TL_DCA']['tl_content']['fields'] += $GLOBALS['TL_DCA']['cta']['fields'];
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'isCta';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['isCta'] = 'ctaColor,ctaDesign';

PaletteManipulator::create()
    ->addField('isCta', 'template_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('hyperlink', 'tl_content');