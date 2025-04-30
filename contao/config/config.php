<?php

use Kiwi\Contao\DesignerBundle\Widget\Backend\IconedSelectMenuWidget;
use Kiwi\Contao\DesignerBundle\Models\ColorModel;

$GLOBALS['design']['ctaDesign'] = [
    'btn' => 'btn btn-{{ctaColor}}',
    'btn-outline' => 'btn btn-outline-{{ctaColor}}',
    'link' => 'textlink textlink-{{ctaColor}}',
];

$GLOBALS['design']['background'] = [
    "none" => "--background{{modifier}}:none;",
    "color" => "--background{{modifier}}:var(--color-{{color}});",
    "picture" => "--background{{modifier}}:url('{{image}}');",
    "video" => "--background{{modifier}}:url('{{video}}');",
    "picture" => "--background{{modifier}}:none;",
    "video" => "--background{{modifier}}:none;",
];

$GLOBALS['design']['backgroundElement'] = [
    "none" => "",
    "color" => "",
    "picture" => "<img data-responsive {{modifiers}} src='{{image}}'/>",
    "video" => "<video data-responsive {{modifiers}} poster='{{poster}}' muted playsinline autoplay loop><source src='{{video}}'/></video>",
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
