<?php

namespace Kiwi\Contao\DesignerBundle\Service;

use Contao\FilesModel;
use Contao\StringUtil;
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

    public function resolveValue($strName, &$strValue, $strBreakpoint = "", $arrBreakpoints = [])
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
            case 'modifiers':
                $GLOBALS['']
                break;
            default:
                if ($GLOBALS['responsive'] ?? false) {
                    $strValue = (new $GLOBALS['responsive']['config']())->arrBreakpoints[$strBreakpoint][$strName] ?? '';
                }
                break;
        }
    }

    public function getClasses($arrData, $strMapping, $strField = "", $strBreakpoint = "", $arrBreakpoints = [])
    {
        if (!$strField) $strField = $strMapping;

        $strClass = $GLOBALS['design'][$strMapping][$this::getProp($arrData, $strField)] ?? false;
        if ($strClass) {
            return preg_replace_callback('/\{{(\w+)}}/', function ($match) use ($arrData, $strBreakpoint, $arrBreakpoints) {
                $matched = $match[0];
                $strName = $match[1];
                $strValue = $this::getProp($arrData, $strName) ?: $matched;
                $this->resolveValue($strName, $strValue, $strBreakpoint, $arrBreakpoints);
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

    public function getStringsArray($arrStyles, $strTarget, $strField): array
    {
        if (!$arrStyles) return [];

        return $this->getClasses($arrStyles, $strTarget, $strField);

        $arrReturn = [];
        $arrBreakpoints = [];

        foreach (array_reverse((new $GLOBALS['responsive']['config']())->arrBreakpoints) ?? [0] as $strBreakpoint => $arrBreakpoint) {
            if ($arrStyles[$strBreakpoint] ?? false) {
                $arrReturn[] = $this->getClasses($arrStyles[$strBreakpoint], $strTarget, $strField, $strBreakpoint, $arrBreakpoints);
                $arrBreakpoints = [];
            } else {
                $arrBreakpoints[] = $strBreakpoint;
            }
        }
        return $arrReturn;
    }

    public function getGlobalStrings($arrData, $strMapping, $strField = "")
    {
        if (!$strField) $strField = $strMapping;

        if (isset($GLOBALS['TL_HOOKS']['getGlobalStrings']) && \is_array($GLOBALS['TL_HOOKS']['resolveValue']))
        {
            foreach ($GLOBALS['TL_HOOKS']['getGlobalStrings'] as $callback)
            {
                System::importStatic($callback[0])->{$callback[1]}($arrData, $strMapping, $strField = "");
            }
        }

        return implode("", $this->getStringsArray([$arrData], $strMapping, $strField));

        if (false && $GLOBALS['responsive'] ?? false) {
            return implode("", $this->getStringsArray(StringUtil::deserialize($arrData[$strField]) ?? [], $strMapping, $strField));
        } else {
            return implode("", $this->getStringsArray([$arrData], $strMapping, $strField));
        }
    }
}