<?php

use Kiwi\Contao\DesignerBundle\DataContainer\ColorListener;

\Contao\System::loadLanguageFile('design');

$GLOBALS['TL_DCA']['cta']['fields'] = [
    'isCta' => [
        'label'     => &$GLOBALS['TL_LANG']['design']['isCta'],
        'reference' => &$GLOBALS['TL_LANG']['design']['isCta'],
        'inputType' => 'checkbox',
        'eval'      => ['submitOnChange' => true, 'tl_class' => 'clr m12'],
        'sql'       => "char(1) NOT NULL default '1'",
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
    ]
];