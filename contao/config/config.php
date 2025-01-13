<?php

use Kiwi\Contao\DesignerBundle\Widget\Backend\IconedSelectMenuWidget;
use Kiwi\Contao\DesignerBundle\Models\ColorModel;

$GLOBALS['design']['ctaDesign'] = [
    'btn' => 'btn btn-{{ctaColor}}',
    'btn-outline' => 'btn btn-outline-{{ctaColor}}',
    'link' => 'textlink textlink-{{ctaColor}}',
];

$GLOBALS['design']['color']['categories'] = [
    'cta',
    'background'
];

$GLOBALS['BE_MOD']['design']['color'] = [
    'tables' => ['tl_color'],
];

$GLOBALS['TL_MODELS']['tl_color'] = ColorModel::class;
