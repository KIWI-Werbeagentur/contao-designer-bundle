<?php

use Kiwi\Contao\DesignerBundle\DataContainer\ColorListener;
use Kiwi\Contao\DesignerBundle\DataContainer\ColorSchemeListener;

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


$arrBackground = ($GLOBALS['responsive'] ?? false) ?
    [
        'background' => [
            'label' => &$GLOBALS['TL_LANG']['design']['background'],
            'reference' => &$GLOBALS['TL_LANG']['design']['background']['options'],
            'inputType' => 'optionalResponsiveSubpalette',
            'responsiveInputType' => 'select',
            'eval' => [
                'mandatory' => true,
                'tl_class' => 'w50 clr',
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
        ],
        'scheme' => [
            'label' => &$GLOBALS['TL_LANG']['design']['scheme'],
            'inputType' => 'optionalResponsive',
            'responsiveInputType' => 'select',
            'foreignKey' => 'tl_color_scheme.title',
            'options_callback' => [ColorSchemeListener::class, 'getSchemes'],
            'eval' => ['tl_class' => 'w50', 'isAssociative' => true],
            'sql' => "blob NULL",
            'relation' => ['type' => 'hasOne', 'load' => 'lazy']
        ],
    ] : [
        'background' => [
            'inputType' => 'select',
            'label' => &$GLOBALS['TL_LANG']['design']['background'],
            'reference' => &$GLOBALS['TL_LANG']['design']['background']['options'],
            'options' => [
                'none',
                'color',
                'picture',
                'video',
            ],
            'eval' => ['submitOnChange' => true, 'tl_class' => 'clr w50'],
            'sql' => "varchar(12) COLLATE ascii_bin NOT NULL default ''"
        ],
        'scheme' => [
            'inputType' => 'select',
            'foreignKey' => 'tl_color_scheme.title',
            'options_callback' => [ColorSchemeListener::class, 'getSchemes'],
            'eval' => ['tl_class' => 'w50', 'isAssociative' => true],
            'sql' => "int(10) unsigned NOT NULL default 0",
            'relation' => ['type' => 'hasOne', 'load' => 'lazy']
        ],
    ];

$GLOBALS['TL_DCA']['background']['fields'] =
    [
        'media' => [
            'label' => &$GLOBALS['TL_LANG']['design']['media'],
            'inputType' => 'fileTree',
            'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'mandatory' => true, 'tl_class' => 'clr'],
            'load_callback' => [
                ['tl_content', 'setSingleSrcFlags']
            ],
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
        'backgroundOverwrite' => [
            'label' => &$GLOBALS['TL_LANG']['design']['backgroundOverwrite'],
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'clr m12'],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'overwriteTable' => [
            'label' => &$GLOBALS['TL_LANG']['design']['overwriteTable'],
            'inputType' => 'select',
            'options_callback' => function () {
                $strDb = \Contao\System::getContainer()->get('doctrine.dbal.default_connection')->getDatabase();
                $arrTables = \Contao\Database::getInstance()->prepare("SELECT DISTINCT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?")->execute($strDb)->fetchEach('TABLE_NAME');
                return $arrTables;
            },
            'eval' => ['mandatory' => true, 'includeBlankOption' => true, 'tl_class' => 'w50', 'submitOnChange' => true],
            'sql' => ['type' => 'string', 'default' => '', 'length' => 64]
        ],
        'overwriteField' => [
            'label' => &$GLOBALS['TL_LANG']['design']['overwriteField'],
            'inputType' => 'select',
            'options_callback' => function ($dc) {
                if (!$dc->activeRecord->overwriteTable) return [];
                $strDb = \Contao\System::getContainer()->get('doctrine.dbal.default_connection')->getDatabase();
                $arrFields = \Contao\Database::getInstance()->prepare("SELECT DISTINCT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?")->execute($dc->activeRecord->overwriteTable, $strDb)->fetchEach('COLUMN_NAME');
                \Contao\Controller::loadDataContainer($dc->activeRecord->overwriteTable);

                $arrOptions = [];
                foreach ($arrFields as $arrField) {
                    if (($GLOBALS['TL_DCA'][$dc->activeRecord->overwriteTable]['fields'][$arrField]['inputType'] ?? '') == 'fileTree') {
                        $arrOptions[$arrField] = $arrField;
                    }
                }
                return $arrOptions;
            },
            'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50', 'submitOnChange' => true],
            'sql' => ['type' => 'string', 'default' => '', 'length' => 64]
        ],
        'overwriteParameter' => [
            'label' => &$GLOBALS['TL_LANG']['design']['overwriteParameter'],
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'default' => '', 'length' => 64]
        ],
        ...$arrBackground
    ];

if (!($GLOBALS['responsive'] ?? false)) {
    $GLOBALS['TL_DCA']['background']['fields']['media']['sql'] = "binary(16) NULL";
    $GLOBALS['TL_DCA']['background']['fields']['color']['sql'] = "int(10) unsigned NOT NULL default 0";
}


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