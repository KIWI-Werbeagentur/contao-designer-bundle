<?php

use Contao\Controller;
use Contao\CoreBundle\DataContainer\PaletteManipulator;

Controller::loadDataContainer('design');
$GLOBALS['TL_DCA']['tl_form_field']['fields'] += $GLOBALS['TL_DCA']['cta']['fields'];
$GLOBALS['TL_DCA']['tl_form_field']['palettes']['__selector__'][] = 'isCta';
$GLOBALS['TL_DCA']['tl_form_field']['subpalettes']['isCta'] = 'ctaColor,ctaDesign';

PaletteManipulator::create()
    ->addField('isCta', 'type_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('submit', 'tl_form_field');