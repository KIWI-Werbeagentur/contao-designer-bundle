<?php

namespace Kiwi\Contao\DesignerBundle\Service;

use Contao\ContentModel;
use Contao\CoreBundle\Fragment\Reference\ContentElementReference;
use Contao\CoreBundle\Routing\ResponseContext\Csp\CspHandler;
use Contao\Database;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\LayoutModel;
use Contao\StringUtil;
use Contao\System;
use Contao\ThemeModel;
use Kiwi\Contao\DesignerBundle\Models\ColorModel;
use Kiwi\Contao\DesignerBundle\Models\ColorSchemeModel;
use Symfony\Component\Filesystem\Path;
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
            case 'modifier':
                // breakpoint suffix, e.g. of data-scheme{{modifier}}; set by kiwi/contao-responsive-base
                // (which resolves after this), empty without it
                $strValue = '';
                break;
            case 'scheme':
                // kiwi/contao-responsive-base passes the scheme of one breakpoint as data (the
                // placeholder then resolves to nothing), otherwise it is the record's field value
                $varScheme = \is_scalar($this->arrData) ? $this->arrData : $strValue;
                if ('inherit' === $varScheme) {
                    $strValue = 'inherit';
                    break;
                }
                $strValue = ColorSchemeModel::findByPk($varScheme)->alias ?? 'inherit';
                break;
            case 'figure':
                $objFigureBuilder = System::getContainer()->get('contao.image.studio')->createFigureBuilder();
                $strRatio = '';
                try {
                    $objFigureBuilder
                        ->fromUuid($this->arrData['image'])
                        ->setSize($this->arrData['size'] ?? null);
                    $figure = $objFigureBuilder->build();
                    $template = new FrontendTemplate('image');
                    $figure->applyLegacyTemplateData($template);
                    $strValue = $template->parse();

                    // the limited picture is sized from the image proportions (see background.scss)
                    $arrImg = $figure->getImage()->getImg();
                    if (($intWidth = (int) ($arrImg['width'] ?? 0)) && ($intHeight = (int) ($arrImg['height'] ?? 0))) {
                        $strRatio = "--background-image-ratio:$intWidth / $intHeight";
                    }
                } catch (\Exception $e) {
                    System::getContainer()->get('monolog.logger.contao')->error($e->getMessage());
                    $strValue = '';
                }
                if ($strValue && !empty($this->arrData['backgroundLimitWidth'])) {
                    $strBlur = empty($this->arrData['backgroundBlur']) ? '' : sprintf("<div class='background__element__layer background__element__layer--blurred' aria-hidden='true' style='--background-blur-brightness:%s'>%s</div>", (int) (($this->arrData['backgroundBrightness'] ?? 0) ?: 100) / 100, $strValue);
                    $strValue = $strBlur . "<div class='background__element__layer background__element__layer--limited' style='$strRatio'>$strValue</div>";
                }
                break;
            case 'poster':
                $strValue = $this->getPosterSrc($strValue);
                break;
            case 'video':
                $strValue = $this->getVersionedPath($strValue);
                break;
            case 'image':
            case 'file':
            case 'picture':
            case 'audio':
                $strValue = FilesModel::findByPk($strValue)?->path ?? '';
                break;
            case 'imageOverwrite':
                $strTable = $this->arrData['overwriteTable'] ?? "";
                $strField = $this->arrData['overwriteField'] ?? "";

                if ($strTable && $strField) {
                    $strUuid = Database::getInstance()->prepare("SELECT {$strField} FROM {$strTable} WHERE alias=?")->execute(Input::get($this->arrData['overwriteParameter'] ?: 'auto_item'))->fetchAssoc()[$strField] ?? 0;
                    $strValue = FilesModel::findByPk($strUuid)->path ?? '';
                }
                break;
            case 'value':
                $strValue = $this->arrData;
                break;
        }

        if (isset($GLOBALS['TL_HOOKS']['resolveDesignValues']) && \is_array($GLOBALS['TL_HOOKS']['resolveDesignValues'])) {
            foreach ($GLOBALS['TL_HOOKS']['resolveDesignValues'] as $callback) {
                System::importStatic($callback[0])->{$callback[1]}($strName, $strValue, $this->arrData);
            }
        }
    }

    /**
     * Resizes the poster through the image studio when a poster size is set, so it is served
     * as a cacheable asset. Without a size the original file path is returned as before.
     */
    protected function getPosterSrc($varUuid): string
    {
        $objFile = FilesModel::findByPk($varUuid);

        if (null === $objFile) {
            return '';
        }

        if (!array_filter(StringUtil::deserialize($this::getProp($this->arrData, 'posterSize'), true))) {
            return $objFile->path;
        }

        try {
            $objImage = System::getContainer()->get('contao.image.studio')
                ->createFigureBuilder()
                ->fromFilesModel($objFile)
                ->setSize($this::getProp($this->arrData, 'posterSize'))
                ->build()
                ->getImage();

            // A poster can only hold a single URL, so the format conversions of the image size
            // (only output as additional <source> elements) would never be used. Prefer the WebP
            // version of the base size if there is one: supported by all current browsers, so it
            // is safe without a fallback (unlike AVIF). Sources with a media query belong to the
            // responsive variants of the size and are skipped. Without a WebP conversion, the
            // result is the same as before.
            foreach ($objImage->getSources() as $arrSource) {
                if (empty($arrSource['media']) && 'image/webp' === ($arrSource['type'] ?? null) && !empty($arrSource['src'])) {
                    return $arrSource['src'];
                }
            }

            return $objImage->getImageSrc();
        } catch (\Exception $e) {
            System::getContainer()->get('monolog.logger.contao')->error($e->getMessage());

            return $objFile->path;
        }
    }

    /**
     * Appends a ?v= cache-busting parameter derived from the file's mtime, like Contao does for
     * layout scripts and stylesheets, so the file can be cached long-term and still refreshes
     * when it is replaced.
     */
    protected function getVersionedPath($varUuid): string
    {
        $strPath = FilesModel::findByPk($varUuid)?->path ?? '';
        $strFile = Path::join(System::getContainer()->getParameter('kernel.project_dir'), $strPath);

        if ('' !== $strPath && is_file($strFile)) {
            $strPath .= '?v=' . substr(md5((string) filemtime($strFile)), 0, 8);
        }

        return $strPath;
    }

    public function getClasses($arrData, $strMapping, $strField = "")
    {
        if (!$strField) $strField = $strMapping;
        $this->arrData = $arrData;

        if (is_array($GLOBALS['design'][$strMapping])) {
            $strClass = $GLOBALS['design'][$strMapping][$this::getProp($arrData, $strField)] ?? '';
        } else {
            $strClass = $GLOBALS['design'][$strMapping] ?? '';
        }

        if ($strClass) {
            $strResult = preg_replace_callback('/\{{(\w+)}}/', function ($match) use ($arrData) {
                $matched = $match[0];
                $strName = $match[1];
                $strValue = $this::getProp($arrData, $strName) ?: $matched;
                $this->resolveValue($strName, $strValue);
                return $strValue;
            }, $strClass);

            $this->allowInlineStyles($strResult);

            return $strResult;
        }
        return "";
    }

    /**
     * Design strings may contain style attributes (e.g. background colors, the "limit width"
     * picture proportions). With Contao's CSP enabled, these are blocked unless their hashes are
     * allowed, so register them the same way as Contao's csp_unsafe_inline_style Twig filter.
     * The values are built by the bundle and the project configuration, not by editors.
     */
    protected function allowInlineStyles(string $strHtml): void
    {
        if (!str_contains($strHtml, 'style=')) {
            return;
        }

        $objResponseContext = System::getContainer()->get('contao.routing.response_context_accessor')->getResponseContext();

        if (!$objResponseContext?->has(CspHandler::class)) {
            return;
        }

        /** @var CspHandler $objCsp */
        $objCsp = $objResponseContext->get(CspHandler::class);

        preg_match_all('/\sstyle=(["\'])(.*?)\1/s', $strHtml, $arrMatches);

        foreach (array_filter($arrMatches[2]) as $strStyle) {
            // the browser hashes the decoded attribute value
            $objCsp
                ->addHash('style-src', html_entity_decode($strStyle, ENT_QUOTES | ENT_HTML5))
                ->addSource('style-src', "'unsafe-hashes'");
        }
    }

    public function getCtaClasses($arrData)
    {
        if (!($this::getProp($arrData, 'isCta') ?: false)) return "";
        return $this->getClasses($arrData, 'ctaDesign');
    }

    /**
     * The section headline is rendered by the parent element (e.g. the core accordion), which
     * usually only knows its children as fragment references holding the model id. The models
     * have already been loaded while building those references, so the lookup is served by the
     * model registry.
     */
    public function getSectionHeadlineClass($varElement): string
    {
        if ($varElement instanceof ContentElementReference) {
            $varElement = $varElement->getContentModel();
        }

        if (is_numeric($varElement)) {
            $varElement = ContentModel::findById((int) $varElement);
        }

        return (string) $this::getProp($varElement, 'sectionHeadlineClass');
    }

    /**
     * Normalizes a headline companion line (topline, subline) for rendering: its text, tag and
     * style class, or null if it has no text. The lines are stored like the core headline (text +
     * tag), see HeadlineLinesMigration.
     */
    public function getHeadlineLine($arrData, string $strField): ?array
    {
        $varValue = StringUtil::deserialize($this::getProp($arrData, $strField), true);
        $strText = (string) ($varValue['value'] ?? '');

        if ('' === trim($strText)) {
            return null;
        }

        return [
            'text' => $strText,
            'tag_name' => ($varValue['unit'] ?? '') ?: ($GLOBALS['design'][$strField . 'Tag'][0] ?? 'div'),
            'class' => (string) $this::getProp($arrData, $strField . 'Class'),
        ];
    }

    public function hasBackground($strBackground)
    {
        $arrBackgrounds = StringUtil::deserialize($strBackground, true);

        foreach ($arrBackgrounds as $arrBackground) {
            if ($arrBackground['background'] != 'none') return true;
        }

        return false;
    }

    public function getGlobalStrings($arrData, $strMapping, $strField = "")
    {
        if (!$strField) $strField = $strMapping;
        return $this->getClasses($arrData, $strMapping, $strField);
    }

    public function getColorVar($id)
    {
        return ColorModel::findByPk($id)->variable ?? 'inherit';
    }

    public function getThemeAndLayout()
    {
        $requestStack = System::getContainer()->get('request_stack');
        $currentRequest = $requestStack->getCurrentRequest();
        $objPage = $currentRequest->attributes->get('pageModel');

        $objLayout = LayoutModel::findByPk($objPage->layout);
        $objTheme = ThemeModel::findByPk($objLayout->pid);
        return "$objTheme->alias--$objLayout->alias";
    }
}
