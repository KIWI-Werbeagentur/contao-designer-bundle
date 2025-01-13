<?php

namespace Kiwi\Contao\DesignerBundle\Service;

use Contao\StringUtil;
use Contao\System;
use Kiwi\Contao\DesignerBundle\Models\ColorModel;

class DesignerFrontendService
{
    public function resolveValue($strName, &$strValue){
        switch($strName){
            case 'ctaColor':
                $strValue = ColorModel::findByPk($strValue)->variable ?? $strValue;
                break;
            default:
                break;
        }
    }

    public function getClasses($arrData, $strMapping)
    {
        $strClass = $GLOBALS['design'][$strMapping][$arrData[$strMapping]] ?? false;
        if ($strClass) {
            return preg_replace_callback('/\{{(\w+)}}/', function ($match) use ($arrData) {
                $matched = $match[0];
                $strName = $match[1];
                $strValue = $arrData[$strName] ?? $matched;
                $this->resolveValue($strName,$strValue);
                return $strValue;
            }, $strClass);
        }
        return "";
    }

    public function getCtaClasses($arrData)
    {
        if(!$arrData['isCta'] ?? false) return "";
        return $this->getClasses($arrData, 'ctaDesign');
    }
}