<?php

namespace Kiwi\Contao\DesignerBundle\Models;

use Contao\Model;
use Contao\StringUtil;

/**
 * @property string $title
 * @property string $variable
 * @property string $value
 */
class ColorModel extends Model
{
    protected static $strTable = "tl_color";

    public static function findApplicable($strApplicapleCat)
    {
        $arrColors = [];
        foreach (ColorModel::findBy('isApplicable', 1) ?? [] as $objColor) {
            $arrCats = StringUtil::deserialize($objColor->category);
            if(!$arrCats || in_array($strApplicapleCat, $arrCats)){
                $arrColors[] = $objColor;
            }
        }
        return $arrColors;
    }
}
