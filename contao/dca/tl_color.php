<?php

use Contao\DC_Table;
use Kiwi\Contao\DesignerBundle\DataContainer\ColorListener;

$GLOBALS['TL_DCA']['tl_color'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
        'onsubmit_callback' => [
            [ColorListener::class, 'updateScssFile'],
        ],
        'ondelete_callback' => [
            [ColorListener::class, 'updateScssFile'],
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
            'fields' => ['value', 'title', 'variable'],
            'format' => '%s',
            'showColumns' => true,
            'label_callback' => [ColorListener::class, 'labelCallback'],
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
        '__selector__' => ['isApplicable'],
        'default' => 'title,isApplicable;variable,value,contrast,highlight',
    ],
    'subpalettes'=>[
        'isApplicable' => 'category',
    ],
    'fields' => [
        'id' => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'title' => [
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'tl_class' => 'w50', 'maxlength' => 255],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'variable' => [
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'rgxp' => 'custom', 'customRgxp' => '#^[a-z]+$#', 'tl_class' => 'w50', 'maxlength' => 32],
            'sql' => "varchar(32) NOT NULL default ''",
        ],
        'value' => [
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'tl_class' => 'w50', 'maxlength' => 255, 'colorpicker' => true, 'decodeEntities' => true],
            'save_callback' => [
                [ColorListener::class, 'valueSaveCallback']
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'contrast' => [
            'inputType'               => 'picker',
            'foreignKey'              => 'tl_color.title',
            'eval'                    => array('tl_class'=>'w50'),
            'sql'                     => "int(10) unsigned NOT NULL default 0",
            'relation'                => array('type'=>'hasOne', 'load'=>'lazy')
        ],
        'highlight' => [
            'inputType'               => 'picker',
            'foreignKey'              => 'tl_color.title',
            'eval'                    => array('tl_class'=>'w50'),
            'sql'                     => "int(10) unsigned NOT NULL default 0",
            'relation'                => array('type'=>'hasOne', 'load'=>'lazy'),
        ],
        'isApplicable' => [
            'exclude'                 => true,
            'inputType'               => 'checkbox',
            'eval'                    => ['submitOnChange' => true, 'tl_class' => 'clr m12'],
            'sql'                     => "char(1) NOT NULL default '1'",
        ],
        'category' => [
            'inputType'               => 'checkboxWizard',
            'options'                 => $GLOBALS['design']['color']['categories'],
            'reference'               => &$GLOBALS['TL_LANG']['tl_color']['category']['options'],
            'eval'                    => array('tl_class'=>'m12 w50', 'multiple'=>true),
            'sql'                     => "text NULL"
        ]
    ],
];
