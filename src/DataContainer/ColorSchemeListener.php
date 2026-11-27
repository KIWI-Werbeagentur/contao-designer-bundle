<?php

namespace Kiwi\Contao\DesignerBundle\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\Database;
use Contao\DataContainer;
use Contao\Message;
use Contao\System;
use Kiwi\Contao\DesignerBundle\Models\ColorSchemeModel;
use Kiwi\Contao\DesignerBundle\Service\SchemesFileRegenerator;
use Symfony\Component\Filesystem\Exception\IOException;

class ColorSchemeListener
{
    public function __construct(
        private readonly SchemesFileRegenerator $regenerator,
    ) {}

    #[AsCallback(table: 'tl_color_scheme', target: 'fields.alias.save')]
    public function generateAlias($varValue, DataContainer $objDca)
    {
        $aliasExists = static function (string $alias) use ($objDca): bool {
            return Database::getInstance()->prepare("SELECT id FROM tl_color_scheme WHERE alias=? AND id!=?")->execute($alias, $objDca->id)->numRows > 0;
        };

        // Generate an alias if there is none
        if (!$varValue) {
            $varValue = System::getContainer()->get('contao.slug')->generate((string)$objDca->activeRecord->title, [], $aliasExists);
        } elseif (preg_match('/^[1-9]\d*$/', $varValue)) {
            throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasNumeric'], $varValue));
        } elseif ($aliasExists($varValue)) {
            throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
        }

        return $varValue;
    }

    #[AsCallback(table: 'tl_content', target: 'fields.scheme.options')]
    public function getSchemes()
    {
        return ["inherit" => &$GLOBALS['TL_LANG']['design']['scheme']['inherit']] + (ColorSchemeModel::findAll()?->fetchEach('title') ?? []);
    }

    public function generateSchemesScss(?DataContainer $objDca = null, ?int $undoId = null): void
    {
        // The record is already saved at this point: report a failed write instead of aborting.
        try {
            $this->regenerator->regenerate();
        } catch (IOException $e) {
            System::getContainer()->get('monolog.logger.contao.error')->error('Could not regenerate the schemes file: ' . $e->getMessage(), ['exception' => $e]);
            Message::addError($e->getMessage());
        }
    }
}
