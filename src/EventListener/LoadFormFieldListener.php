<?php

namespace Kiwi\Contao\DesignerBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Form;
use Contao\Widget;
use Kiwi\Contao\DesignerBundle\Service\DesignerFrontendService;

/**
 * Adds the call-to-action classes of a form generator submit button to its input, which
 * form_submit prints as inputClasses.
 *
 * loadFormField fires once per form generator field, after the widget is built from its
 * tl_form_field row and before it is validated and rendered - so the classes are in place for
 * the one regular render. This used to be a parseWidget listener, which only runs after rendering
 * and therefore had to render every widget a second time.
 */
#[AsHook('loadFormField')]
class LoadFormFieldListener
{
    public function __construct(private readonly DesignerFrontendService $designerFrontendService)
    {
    }

    public function __invoke(Widget $objWidget, string $strForm, array $arrForm, Form $objForm): Widget
    {
        $strClasses = $this->designerFrontendService->getCtaClasses($objWidget);

        if ($strClasses) {
            $objWidget->inputClasses = trim(($objWidget->inputClasses ?? '') . ' ' . $strClasses);
        }

        return $objWidget;
    }
}
