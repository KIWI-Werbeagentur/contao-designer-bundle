<?php

namespace Kiwi\Contao\DesignerBundle\Command;

use Contao\CoreBundle\Framework\ContaoFramework;
use Kiwi\Contao\DesignerBundle\DataContainer\ColorSchemeListener;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'kiwi:designer:generate-schemes', description: 'Regenerates the _schemes.scss file from the current color scheme configuration.')]
class GenerateSchemesCommand extends Command
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly ColorSchemeListener $colorSchemeListener,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->framework->initialize();

        try {
            $this->colorSchemeListener->generateSchemesScss();
        } catch (\Throwable $e) {
            $output->writeln('<error>Failed to regenerate _schemes.scss: ' . $e->getMessage() . '</error>');

            return Command::FAILURE;
        }

        $output->writeln('_schemes.scss has been regenerated.');

        return Command::SUCCESS;
    }
}
