<?php

namespace Kiwi\Contao\DesignerBundle\DataContainer;

use Codefog\HasteBundle\Formatter;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\System;

class TemplateListener
{
    #[AsCallback('tl_article', 'fields.customTpl.load')]
    #[AsCallback('tl_content', 'fields.customTpl.load')]
    #[AsCallback('tl_module', 'fields.customTpl.load')]
    public function renameTemplates($varValue, DataContainer $objDca)
    {
        System::loadLanguageFile('templates');

        $GLOBALS['TL_DCA'][$objDca->table]['fields'][$objDca->field]['reference'] = &$GLOBALS['TL_LANG']['templates'][$objDca->table];

        return $varValue;
    }
}
