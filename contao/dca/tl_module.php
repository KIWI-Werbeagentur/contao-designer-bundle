<?php

use \Kiwi\Contao\DesignerBundle\DataContainer\TemplateListener;

$GLOBALS['TL_DCA']['tl_module']['fields']['customTpl']['load_callback'][] = [TemplateListener::class, 'renameTemplates'];