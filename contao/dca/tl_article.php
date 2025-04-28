<?php

use Kiwi\Contao\DesignerBundle\DataContainer\TemplateListener;
use \Kiwi\Contao\CmxBundle\DataContainer\PaletteManipulatorExtended;
use \Kiwi\Contao\DesignerBundle\DataContainer\ColorListener;
use \Contao\ArticleModel;
use \Contao\Input;

$GLOBALS['TL_DCA']['tl_article']['fields']['customTpl']['load_callback'][] = [TemplateListener::class, 'renameTemplates'];

$GLOBALS['TL_DCA']['tl_article']['fields']['singleSRC'] = [
    'inputType' => 'fileTree',
    'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'mandatory' => true, 'tl_class' => 'clr'],
    'load_callback' => [
        ['tl_content', 'setSingleSrcFlags']
    ],
    'sql' => "binary(16) NULL"
];

$GLOBALS['TL_DCA']['tl_article']['fields']['color'] = [
    'inputType' => 'iconedSelect',
    'options_callback' => function(){
        return ColorListener::getCategoryOptions(['table'=>'tl_article', 'field'=>'color']);
    },
    'icon_callback' => [ColorListener::class, 'getIcons'],
    'category' => 'background',
    'eval' => ['tl_class' => 'clr w50', 'mandatory' => true]
];

$GLOBALS['TL_DCA']['tl_article']['fields']['background'] = [
    'inputType' => 'optionalResponsiveSubpalette',
    'responsiveInputType' => 'select',
    'eval' => [
        'mandatory' => true,
        'tl_class' => 'clr',
        'submitOnChange' => true,
    ],
    'options' => [
        'none',
        'color',
        'picture',
        'video',
    ],
    'subpalettes' => [
        'none' => [],
        'color' => [
            'color' => 'color'
        ],
        'picture' => [
            'image' => 'singleSRC',
        ],
        'video' => [
            'video' => 'singleSRC',
            'poster' => [
                'inputType' => 'fileTree',
                'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'mandatory' => true, 'tl_class' => 'clr'],
            ],
        ],
    ],
    'sql' => "blob NULL"
];

PaletteManipulatorExtended::create()
    ->addField('background', 'template_legend', PaletteManipulatorExtended::POSITION_APPEND)
    ->applyToPalette('default', 'tl_article');