<?php

namespace Kiwi\Contao\DesignerBundle\DataContainer;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;

class SectionHeadlineListener
{
    /**
     * sectionHeadline is added to the palettes of nested elements (accordion, tabs, …) by
     * onpalette callbacks, so the class select can only be placed after those have run.
     */
    #[AsCallback(table: 'tl_content', target: 'config.onpalette', priority: -100)]
    public function addSectionHeadlineClass(string $strPalette, DataContainer $objDca): string
    {
        if (!preg_match('/(^|[,;])\s*sectionHeadline\s*([,;]|$)/', $strPalette) || str_contains($strPalette, 'sectionHeadlineClass')) {
            return $strPalette;
        }

        return PaletteManipulator::create()
            ->addField('sectionHeadlineClass', 'sectionHeadline')
            ->applyToString($strPalette);
    }
}
