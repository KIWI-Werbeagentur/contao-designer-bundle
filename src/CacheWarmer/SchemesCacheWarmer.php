<?php

namespace Kiwi\Contao\DesignerBundle\CacheWarmer;

use Contao\CoreBundle\Framework\ContaoFramework;
use Kiwi\Contao\DesignerBundle\Service\SchemesFileRegenerator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * Re-renders _schemes.scss during `cache:warmup`, so template and hook changes brought in
 * by a bundle update reach the CSS without saving a scheme. Optional and non-fatal.
 */
class SchemesCacheWarmer implements CacheWarmerInterface
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly SchemesFileRegenerator $regenerator,
        private readonly ?LoggerInterface $logger = null,
    ) {}

    public function isOptional(): bool
    {
        return true;
    }

    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        try {
            // Populates $GLOBALS['scheme'] and the hooks and makes the models usable.
            $this->framework->initialize();
        } catch (\Throwable $e) {
            // Expected where no database or Contao setup is available (e.g. a build step).
            $this->logger?->warning('Skipped regenerating the schemes SCSS: the Contao framework could not be initialized.', ['exception' => $e]);

            return [];
        }

        try {
            $this->regenerator->regenerate();
        } catch (\Throwable $e) {
            // The file keeps its previous content until the next successful run.
            $this->logger?->error('Failed to regenerate the schemes SCSS; it stays at its previous state: {message}', ['message' => $e->getMessage(), 'exception' => $e]);
        }

        return [];
    }
}
