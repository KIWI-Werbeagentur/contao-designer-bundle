<?php

use Kiwi\Contao\DesignerBundle\Widget\Backend\IconedSelectMenuWidget;
use Kiwi\Contao\DesignerBundle\Models\ColorModel;

$GLOBALS['design']['ctaDesigns'] = [
    'btn' => 'btn btn-{{color}}',
    'btn-outline' => 'btn btn-outline-{{color}}',
    'link' => 'textlink textlink-{{color}}',
];

$GLOBALS['design']['color']['categories'] = [
    'cta',
    'background'
];

$GLOBALS['BE_MOD']['design']['color'] = [
    'tables' => ['tl_color'],
];

$GLOBALS['TL_MODELS']['tl_color'] = ColorModel::class;
