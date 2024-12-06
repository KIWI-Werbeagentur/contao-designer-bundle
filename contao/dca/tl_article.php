<?php

use Kiwi\Contao\DesignerBundle\DataContainer\TemplateListener;

$GLOBALS['TL_DCA']['tl_article']['fields']['customTpl']['load_callback'][] = [TemplateListener::class, 'renameTemplates'];