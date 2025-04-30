<?php

namespace Kiwi\Contao\DesignerBundle\Service;

use Contao\FilesModel;
use Contao\System;
use Kiwi\Contao\DesignerBundle\Models\ColorModel;

class DesignerFrontendService
{
    public static function getProp($varTarget, $strProp)
    {
        if (is_array($varTarget)) {
            return $varTarget[$strProp] ?? "";
        }
        if (is_object($varTarget)) {
            return $varTarget->{$strProp} ?? "";
        }
        return "";
    }

    public function resolveValue($strName, &$strValue)
    {
        switch ($strName) {
            case 'color':
            case 'ctaColor':
                $strValue = ColorModel::findByPk($strValue)->variable ?? $strValue;
                break;
            case 'poster':
            case 'image':
            case 'file':
            case 'picture':
            case 'video':
            case 'audio':
                $strValue = FilesModel::findByPk($strValue)->path;
                break;
        }
    }

    public function getClasses($arrData, $strMapping, $strField = "")
    {
        if (!$strField) $strField = $strMapping;

        $strClass = $GLOBALS['design'][$strMapping][$this::getProp($arrData, $strField)] ?? false;
        if ($strClass) {
            return preg_replace_callback('/\{{(\w+)}}/', function ($match) use ($arrData) {
                $matched = $match[0];
                $strName = $match[1];
                $strValue = $this::getProp($arrData, $strName) ?: $matched;
                $this->resolveValue($strName, $strValue);
                return $strValue;
            }, $strClass);
        }
        return "";
    }

    public function getCtaClasses($arrData)
    {
        if (!($this::getProp($arrData, 'isCta') ?: false)) return "";
        return $this->getClasses($arrData, 'ctaDesign');
    }

    public function getGlobalStrings($arrData, $strMapping, $strField = "")
    {
        if (!$strField) $strField = $strMapping;
        return $this->getClasses($arrData, $strMapping, $strField);
    }
}