<?php

namespace Kiwi\Contao\DesignerBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;

#[AsHook('generatePage')]
class GeneratePageListener
{
    public function __invoke(PageModel $objPage, LayoutModel $objLayout)
    {
        $arrFramework = StringUtil::deserialize($objLayout->framework, true);

        foreach ($arrFramework as $strFile) {
            switch ($strFile) {
                case 'color_styles':
                    //In normales CSS?
                    $GLOBALS['TL_FRAMEWORK_CSS'][] = '/bundles/kiwidesigner/colors.scss';
                    break;
                case 'background_styles':
                    $GLOBALS['TL_FRAMEWORK_CSS'][] = '/bundles/kiwidesigner/background.scss';
                    break;
                default:
                    break;
            }
        }

        $arrFramework = array_filter($arrFramework, function($v) {
            return !in_array($v,['color_styles']);
        });
        // remove styles from array, so files do not get added twice
        $objLayout->framework = serialize($arrFramework);
    }
}
