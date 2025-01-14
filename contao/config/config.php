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

$GLOBALS['design']['headlineClass'] = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'display-1', 'display-2', 'display-3', 'display-4', 'display-5', 'display-6'];

$GLOBALS['BE_MOD']['design']['color'] = [
    'tables' => ['tl_color'],
];

$GLOBALS['TL_MODELS']['tl_color'] = ColorModel::class;
