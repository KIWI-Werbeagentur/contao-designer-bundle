<?php

namespace Kiwi\Contao\DesignerBundle\Twig;

use Kiwi\Contao\DesignerBundle\Service\DesignerFrontendService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DesignerExtension extends AbstractExtension
{
    public function __construct(protected DesignerFrontendService $designerFrontendService)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getClasses', [$this->designerFrontendService, 'getClasses']),
            new TwigFunction('getCtaClasses', [$this->designerFrontendService, 'getCtaClasses']),
            new TwigFunction('getGlobalStrings', [$this->designerFrontendService, 'getGlobalStrings']),
            new TwigFunction('getColorVar', [$this->designerFrontendService, 'getColorVar']),
            new TwigFunction('getThemeAndLayout', [$this->designerFrontendService, 'getThemeAndLayout']),
            new TwigFunction('hasBackground', [$this->designerFrontendService, 'hasBackground']),
        ];
    }
}