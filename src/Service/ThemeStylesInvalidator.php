<?php

namespace Kiwi\Contao\DesignerBundle\Service;

use Contao\Automator;
use Contao\CoreBundle\Framework\ContaoFramework;
use Kiwi\Contao\BootstrapBundle\Service\ThemeStylesInvalidator as BootstrapThemeStylesInvalidator;
use Psr\Log\LoggerInterface;

/**
 * Makes Contao recompile the theme stylesheets after a shared partial (_schemes.scss,
 * _colorvars.scss) changed.
 *
 * With kiwi/contao-bootstrap installed this delegates to its invalidator, which also bumps
 * the stylesheet versions. Without it only the script cache is purged.
 */
class ThemeStylesInvalidator
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly ?BootstrapThemeStylesInvalidator $bootstrapInvalidator = null,
        private readonly ?LoggerInterface $logger = null,
    ) {}

    /**
     * Failures are logged, not thrown: the partial has already been written at this point.
     */
    public function invalidate(): void
    {
        if ($this->bootstrapInvalidator !== null) {
            $this->bootstrapInvalidator->invalidate();
        } else {
            try {
                $this->framework->initialize();
                $this->framework->createInstance(Automator::class)->purgeScriptCache();
            } catch (\Throwable $e) {
                $this->logger?->error('Failed to purge the script cache: {message}', ['message' => $e->getMessage(), 'exception' => $e]);
            }
        }
    }
}
