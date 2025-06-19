<?php

namespace Kiwi\Contao\DesignerBundle\Service;

use Contao\Database;
use Contao\FilesModel;
use Contao\LayoutModel;
use Contao\StringUtil;
use Contao\System;
use Contao\ThemeModel;
use Kiwi\Contao\DesignerBundle\Models\ColorModel;
use Kiwi\Contao\DesignerBundle\Models\ColorSchemeModel;
use Symfony\Component\HttpFoundation\RequestStack;

class DesignerFrontendService
{
    protected $arrData = [];

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
            case 'scheme':
                $strValue = ColorSchemeModel::findByPk($this->arrData)->alias ?? 'inherit';
                break;
            case 'poster':
            case 'image':
            case 'file':
            case 'picture':
            case 'video':
            case 'audio':
                $strValue = FilesModel::findByPk($strValue)->path;
                break;
            case 'imageOverwrite':
                $strTable = $this->arrData['overwriteTable'] ?? "";
                $strField = $this->arrData['overwriteField'] ?? "";

                if($strTable && $strField) {
                    $strUuid = Database::getInstance()->prepare("SELECT {$strField} FROM {$strTable}")->execute()->fetchAssoc()[$strField];
                    $strValue = FilesModel::findByPk($strUuid)->path;
                }
                break;
        }

        if (isset($GLOBALS['TL_HOOKS']['resolveDesignValues']) && \is_array($GLOBALS['TL_HOOKS']['resolveDesignValues']))
        {
            foreach ($GLOBALS['TL_HOOKS']['resolveDesignValues'] as $callback)
            {
                System::importStatic($callback[0])->{$callback[1]}($strName, $strValue, $this->arrData);
            }
        }
    }

    public function getClasses($arrData, $strMapping, $strField = "")
    {
        if (!$strField) $strField = $strMapping;
        $this->arrData = $arrData;

        $strClass = $GLOBALS['design'][$strMapping][$this::getProp($arrData, $strField)] ?? $GLOBALS['design'][$strMapping] ?? false;

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

    public function hasBackground($strBackground)
    {
        $arrBackgrounds = StringUtil::deserialize($strBackground);

        foreach($arrBackgrounds as $arrBackground){
            if($arrBackground['background'] != 'none') return true;
        }

        return false;
    }

    public function getGlobalStrings($arrData, $strMapping, $strField = "")
    {
        if (!$strField) $strField = $strMapping;
        return $this->getClasses($arrData, $strMapping, $strField);
    }

    public function getColorVar($id){
        return ColorModel::findByPk($id)->variable ?? 'inherit';
    }

    public function getThemeAndLayout(){
        $requestStack = System::getContainer()->get('request_stack');
        $currentRequest = $requestStack->getCurrentRequest();
        $objPage = $currentRequest->attributes->get('pageModel');

        $objLayout = LayoutModel::findByPk($objPage->layout);
        $objTheme = ThemeModel::findByPk($objLayout->pid);
        return "$objTheme->alias--$objLayout->alias";
    }
}