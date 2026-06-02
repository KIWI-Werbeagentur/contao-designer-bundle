<?php

use Contao\Controller;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Kiwi\Contao\DesignerBundle\DataContainer\CtaListener;

Controller::loadDataContainer('design');
$GLOBALS['TL_DCA']['tl_form_field']['fields'] += $GLOBALS['TL_DCA']['cta']['fields'];
$GLOBALS['TL_DCA']['tl_form_field']['palettes']['__selector__'][] = 'isCta';
$GLOBALS['TL_DCA']['tl_form_field']['subpalettes']['isCta'] = 'ctaColor,ctaDesign';
$GLOBALS['TL_DCA']['tl_form_field']['config']['onbeforesubmit_callback'][] = [CtaListener::class, 'beforeSubmitCallback'];
$GLOBALS['TL_DCA']['tl_form_field']['fields']['isCta']['load_callback'][] = [CtaListener::class, 'loadCallback'];

PaletteManipulator::create()
    ->addField('isCta', 'template_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('submit', 'tl_form_field');