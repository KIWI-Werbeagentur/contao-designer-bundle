<?php

use Kiwi\Contao\DesignerBundle\DataContainer\ColorListener;

\Contao\System::loadLanguageFile('design');

$GLOBALS['TL_DCA']['cta']['fields'] = [
    'isCta' => [
        'label' => &$GLOBALS['TL_LANG']['design']['isCta'],
        'reference' => &$GLOBALS['TL_LANG']['design']['isCta'],
        'inputType' => 'checkbox',
        'eval' => ['submitOnChange' => true, 'tl_class' => 'clr m12'],
        'sql' => "char(1) NOT NULL default '1'",
    ],
    'ctaColor' => [
        'label' => &$GLOBALS['TL_LANG']['design']['ctaColor'],
        'inputType' => 'iconedSelect',
        'options_callback' => [ColorListener::class, 'getCategoryOptions'],
        'icon_callback' => [ColorListener::class, 'getIcons'],
        'category' => 'cta',
        'eval' => ['tl_class' => 'clr w50', 'mandatory' => true],
        'sql' => "varchar(35) NOT NULL default ''"
    ],
    'ctaDesign' => [
        'label' => &$GLOBALS['TL_LANG']['design']['ctaDesign'],
        'reference' => &$GLOBALS['TL_LANG']['design']['ctaDesign'],
        'inputType' => 'select',
        'options' => array_keys($GLOBALS['design']['ctaDesign']),
        'eval' => ['tl_class' => 'w50', 'mandatory' => true],
        'sql' => "varchar(35) NOT NULL default ''"
    ],
];

$GLOBALS['TL_DCA']['background']['fields'] = [
    'media' => [
        'label' => &$GLOBALS['TL_LANG']['design']['media'],
        'inputType' => 'fileTree',
        'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'mandatory' => true, 'tl_class' => 'clr'],
        'load_callback' => [
            ['tl_content', 'setSingleSrcFlags']
        ]
    ],
    'color' => [
        'label' => &$GLOBALS['TL_LANG']['design']['color'],
        'inputType' => 'iconedSelect',
        'options_callback' => function () {
            return ColorListener::getCategoryOptions(['table' => 'tl_article', 'field' => 'color']);
        },
        'icon_callback' => [ColorListener::class, 'getIcons'],
        'category' => 'background',
        'eval' => ['tl_class' => 'clr w50', 'mandatory' => true]
    ],
    'background' => [
        'label' => &$GLOBALS['TL_LANG']['design']['background'],
        'reference' => &$GLOBALS['TL_LANG']['design']['background']['options'],
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
                'image' => 'media',
            ],
            'video' => [
                'video' => 'media',
                'poster' => [
                    'label' => &$GLOBALS['TL_LANG']['design']['poster'],
                    'inputType' => 'fileTree',
                    'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'mandatory' => true, 'tl_class' => 'clr'],
                ],
            ],
        ],
        'sql' => "blob NULL"
    ]
];

$GLOBALS['TL_DCA']['headline']['fields'] = [
    'topline' => [
        'label' => &$GLOBALS['TL_LANG']['design']['topline'],
        'search' => true,
        'inputType' => 'text',
        'eval' => ['maxlength' => 255, 'tl_class' => 'clr w50'],
        'sql' => "varchar(255) NOT NULL default ''"
    ],
    'headlineClass' => [
        'label' => &$GLOBALS['TL_LANG']['design']['headlineClass'],
        'inputType' => 'select',
        'options' => &$GLOBALS['design']['headlineClass'],
        'reference' => &$GLOBALS['TL_LANG']['design']['headlineClass']['options'],
        'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
        'sql' => ['name' => 'headlineClass', 'type' => 'string', 'default' => '', 'length' => 64, 'customSchemaOptions' => ['collation' => 'ascii_bin']]
    ],
    'subline' => [
        'label' => &$GLOBALS['TL_LANG']['design']['subline'],
        'inputType' => 'text',
        'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
        'sql' => "varchar(255) NOT NULL default ''"
    ]
];