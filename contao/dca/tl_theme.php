<?php

use Kiwi\Contao\DesignerBundle\DataContainer\ThemeListener;

$GLOBALS['TL_DCA']['tl_theme']['config']['onsubmit_callback'][] =  [ThemeListener::class, 'initScssFile'];