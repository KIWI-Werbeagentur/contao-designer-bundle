<?php

use Kiwi\Contao\DesignerBundle\Models\ColorSchemeCategoryModel;
use Kiwi\Contao\DesignerBundle\Models\ColorSchemeModel;
use Kiwi\Contao\DesignerBundle\Widget\Backend\IconedSelectMenuWidget;
use Kiwi\Contao\DesignerBundle\Models\ColorModel;

$GLOBALS['design']['ctaDesign'] = [
    'btn' => 'btn btn-{{ctaColor}}',
    'btn-outline' => 'btn btn-outline-{{ctaColor}}',
    'link' => 'textlink textlink-{{ctaColor}}',
];

$GLOBALS['design']['background'] = [
    "none" => "--background{{modifier}}:none;",
    "color" => "--background{{modifier}}:var(--color-{{color}});--contrast{{modifier}}:var(--color-{{contrast}});",
    "picture" => "--background{{modifier}}:url('{{image}}');",
    "video" => "--background{{modifier}}:url('{{video}}');",
    "picture" => "--background{{modifier}}:none;",
    "video" => "--background{{modifier}}:none;",
];

$GLOBALS['design']['backgroundElement'] = [
    "none" => "",
    "color" => "<div class='background__element background__element--color' data-responsive {{modifiers}} style='--background-color:var(--color-{{color}})'></div>",
    "picture" => "<div class='background__element background__element--picture' data-responsive {{modifiers}}>{{figure}}</div>",
    "video" => "<div class='background__element background__element--video' data-responsive {{modifiers}}><video class='background__element__video' poster='{{poster}}' muted playsinline autoplay loop><source src='{{video}}'/></video></div>",
];
$GLOBALS['design']['backgroundOverwrite'] = [1=>"<div class='background__element background__element--picture' data-responsive {{modifiersAll}}><img class='background__element__image' src='{{imageOverwrite}}'/></div>"];

$GLOBALS['design']['scheme'] = "data-scheme{{modifier}}={{scheme}}";

$GLOBALS['design']['color']['categories'] = [
    'cta',
    'background'
];

$GLOBALS['design']['tl_content']['background'] = ['element_group'];

$GLOBALS['design']['headlineClass'] = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'display-1', 'display-2', 'display-3', 'display-4', 'display-5', 'display-6'];

$GLOBALS['BE_MOD']['design']['color'] = [
    'tables' => ['tl_color'],
];

$GLOBALS['BE_MOD']['design']['color_scheme'] = [
    'tables' => ['tl_color_scheme'],
];

$GLOBALS['TL_MODELS']['tl_color'] = ColorModel::class;
$GLOBALS['TL_MODELS']['tl_color_scheme'] = ColorSchemeModel::class;
$GLOBALS['TL_MODELS']['tl_color_scheme_category'] = ColorSchemeCategoryModel::class;
