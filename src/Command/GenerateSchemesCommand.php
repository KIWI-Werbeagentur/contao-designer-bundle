<?php

namespace Kiwi\Contao\DesignerBundle\Command;

use Contao\CoreBundle\Framework\ContaoFramework;
use Kiwi\Contao\DesignerBundle\Service\SchemesFileRegenerator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'kiwi:designer:generate-schemes', description: 'Regenerates the _schemes.scss file from the current color scheme configuration.')]
class GenerateSchemesCommand extends Command
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly SchemesFileRegenerator $regenerator,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->framework->initialize();

        try {
            $written = $this->regenerator->regenerate();
        } catch (\Throwable $e) {
            $output->writeln('<error>Failed to regenerate _schemes.scss: ' . $e->getMessage() . '</error>');

            return Command::FAILURE;
        }

        $output->writeln($written ? '_schemes.scss has been regenerated.' : '_schemes.scss is up to date.');

        return Command::SUCCESS;
    }
}
