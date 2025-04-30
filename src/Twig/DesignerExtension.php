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
            new TwigFunction('getCtaClasses', [$this->designerFrontendService, 'getCtaClasses']),
            new TwigFunction('getGlobalStrings', [$this->designerFrontendService, 'getGlobalStrings']),
        ];
    }
}