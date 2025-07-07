<?php

use Contao\DC_Table;
use Kiwi\Contao\CmxBundle\DataContainer\PaletteManipulatorExtended;
use Kiwi\Contao\DesignerBundle\DataContainer\ColorListener;
use Kiwi\Contao\DesignerBundle\DataContainer\ColorSchemeListener;

$GLOBALS['TL_DCA']['tl_color_scheme'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
        'onsubmit_callback' => [
            [ColorSchemeListener::class, 'generateSchemesScss'],
        ],
        'ondelete_callback' => [
            [ColorSchemeListener::class, 'generateSchemesScss'],
        ],
    ],

    'list' => [
        'sorting' => [
            'mode' => 0,
            'flag' => 1,
            'panelLayout' => 'sort,limit',
            'fields' => ['title'],
        ],
        'label' => [
            'fields' => ['title'],
            'format' => '%s',
            'showColumns' => true,
        ],
        'operations' => [
            'edit' => [
                'href' => 'act=edit',
                'icon' => 'edit.svg',
            ],
            'copy' => [
                'href' => 'act=copy',
                'icon' => 'copy.svg',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteColorConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"',
            ],
        ],
    ],

    'palettes' => [
        'default' => 'title,alias;{colors_legend}',
    ],
    'fields' => [
        'id' => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'pid' => [
            'foreignKey' => 'tl_color_scheme_category.title',
            'sql' => "int(10) unsigned NOT NULL default 0",
            'relation' => ['type' => 'belongsTo', 'load' => 'lazy']
        ],
        'title' => [
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'tl_class' => 'w50', 'maxlength' => 255],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'alias' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'alias', 'doNotCopy' => true, 'unique' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'save_callback' => [
                [ColorSchemeListener::class, 'generateAlias']
            ],
            'sql' => "varchar(255) BINARY NOT NULL default ''"
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ]
    ],
];

$arrFields = [];
foreach ($GLOBALS['scheme']['fields'] ?? [] as $strField) {
    $GLOBALS['TL_DCA']['tl_color_scheme']['fields'][$strField] = [
        'label' => &$GLOBALS['TL_LANG']['design']['scheme'][$strField],
        'inputType' => 'iconedSelect',
        'options_callback' => [ColorListener::class, 'getCategoryOptions'],
        'icon_callback' => [ColorListener::class, 'getIcons'],
        'eval' => ['tl_class' => 'w25', 'mandatory' => true],
        'sql' => "varchar(35) NOT NULL default ''"
    ];

    $GLOBALS['TL_DCA']['tl_color_scheme']['fields'][$strField . "Brightness"] = [
        'label' => "",
        'inputType' => 'select',
        'options_callback' => function () {
            $arrOptions = [];
            for ($i = 0; $i <= 20; $i++) {
                $arrOptions[] = $i * 5;
            }
            return $arrOptions;
        },
        'eval' => ['tl_class' => 'w25', 'includeBlankOption' => true],
        'sql' => "varchar(35) NOT NULL default ''"
    ];

    $arrFields[] = $strField;
    $arrFields[] = $strField . "Brightness";
}

PaletteManipulatorExtended::create()
    ->addField($arrFields, 'colors_legend')
    ->applyToPalette('default', 'tl_color_scheme');