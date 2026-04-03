<?php

namespace Kiwi\Contao\DesignerBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class SyncColorVars extends AbstractMigration
{
    public function __construct(
        private readonly Connection $connection,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
    }

    public function getName(): string
    {
        return 'Sync Color Vars';
    }

    public function shouldRun(): bool
    {
        if (getenv('DESIGNER_BUNDLE_COLOR_SYNC') === 'false') {
            return false;
        }

        if (!$this->connection->createSchemaManager()->tablesExist(['tl_color'])) {
            return false;
        }

        $scssColors = $this->parseScssColors();
        $dbColors = $this->getDbColors();

        if (!empty(array_diff_key($scssColors, $dbColors))) {
            return true;
        }

        if (!empty(array_diff_key($dbColors, $scssColors))) {
            return true;
        }

        foreach ($scssColors as $variable => $value) {
            if (isset($dbColors[$variable]) && $dbColors[$variable]['value'] !== $value) {
                return true;
            }
        }

        return false;
    }

    public function run(): MigrationResult
    {
        $scssColors = $this->parseScssColors();
        $dbColors = $this->getDbColors();
        $messages = [];

        $categories = $GLOBALS['design']['color']['categories'];

        foreach ($scssColors as $variable => $value) {
            if (!isset($dbColors[$variable])) {
                $this->connection->insert('tl_color', [
                    'tstamp' => time(),
                    'title' => $variable,
                    'variable' => $variable,
                    'value' => $value,
                    'isApplicable' => '1',
                    'category' => serialize($categories)
                ]);
                $messages[] = "Added: $variable ($value)";
            } elseif ($dbColors[$variable]['value'] !== $value) {
                $this->connection->update('tl_color',
                    ['value' => $value, 'tstamp' => time()],
                    ['id' => $dbColors[$variable]['id']]
                );
                $messages[] = "Updated: $variable ({$dbColors[$variable]['value']} → $value)";
            }
        }

        foreach ($dbColors as $variable => $row) {
            if (!isset($scssColors[$variable])) {
                $this->connection->delete('tl_color', ['id' => $row['id']]);
                $messages[] = "Removed: $variable";
            }
        }

        return $this->createResult(true, implode(', ', $messages));
    }

    private function parseScssColors(): array
    {
        $filePath = $this->projectDir . '/files/themes/_colorvars.scss';

        if (!file_exists($filePath)) {
            return [];
        }

        $content = file_get_contents($filePath);
        $colors = [];

        if (preg_match('/:root\s*\{([^}]*)\}/', $content, $rootMatch)) {
            preg_match_all('/--color-([a-z][a-z0-9-]*):\s*([^;]+);/', $rootMatch[1], $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $colors[trim($match[1])] = trim($match[2]);
            }
        }

        return $colors;
    }

    private function getDbColors(): array
    {
        $rows = $this->connection->fetchAllAssociative('SELECT id, variable, value FROM tl_color');
        $colors = [];

        foreach ($rows as $row) {
            $colors[$row['variable']] = [
                'id' => $row['id'],
                'value' => $row['value']
            ];
        }

        return $colors;
    }
}