<?php

namespace Kiwi\Contao\DesignerBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * The topline and subline used to be plain text fields rendered as paragraphs. They are now
 * stored like the core headline (text + tag): existing lines are converted with the paragraph
 * tag, so they keep rendering exactly as before.
 */
class HeadlineLinesMigration extends AbstractMigration
{
    private const array FIELDS = ['topline', 'subline'];

    // Same text limit as the core headline, so the serialized value fits the varchar(255) column
    private const int MAX_LENGTH = 200;

    public function __construct(
        private readonly Connection $connection,
        #[Autowire(service: 'monolog.logger.contao.general')]
        private readonly LoggerInterface $logger,
    ) {
    }

    public function getName(): string
    {
        return 'Designer: store topline and subline with their tag';
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['tl_content'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_content');

        foreach (self::FIELDS as $field) {
            if (isset($columns[strtolower($field)]) && $this->fetchPlain($field)) {
                return true;
            }
        }

        return false;
    }

    public function run(): MigrationResult
    {
        $columns = $this->connection->createSchemaManager()->listTableColumns('tl_content');
        $converted = 0;
        $cut = [];

        foreach (self::FIELDS as $field) {
            if (!isset($columns[strtolower($field)])) {
                continue;
            }

            foreach ($this->fetchPlain($field) as $id => $value) {
                if (mb_strlen($value) > self::MAX_LENGTH) {
                    $value = mb_substr($value, 0, self::MAX_LENGTH);
                    $cut[] = \sprintf('tl_content.%s ID %d', $field, $id);
                }

                $converted += $this->connection->update('tl_content', [$field => serialize(['value' => $value, 'unit' => 'p'])], ['id' => $id]);
            }
        }

        $message = \sprintf('Converted %d topline/subline values.', $converted);

        if ($cut) {
            $message .= \sprintf(' Cut to %d characters: %s', self::MAX_LENGTH, implode(', ', $cut));
            $this->logger->warning(\sprintf('Designer headline lines migration cut these values to %d characters: %s', self::MAX_LENGTH, implode(', ', $cut)));
        }

        return $this->createResult(true, $message);
    }

    /**
     * @return array<int, string> the values not yet stored as text + tag, keyed by ID
     */
    private function fetchPlain(string $field): array
    {
        return $this->connection->fetchAllKeyValue(\sprintf("SELECT id, %1\$s FROM tl_content WHERE %1\$s != '' AND %1\$s NOT LIKE 'a:2:{%%'", $this->connection->quoteIdentifier($field)));
    }
}
