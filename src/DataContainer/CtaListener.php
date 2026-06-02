<?php

namespace Kiwi\Contao\DesignerBundle\DataContainer;

use Contao\DataContainer;
use Contao\Database;

class CtaListener
{
    private array $ctaDefaultOff = [
        'download',
        'downloads',
    ];

    private const string SENTINEL = '3';

    public function loadCallback(mixed $value, DataContainer $dc): mixed
    {
        if ($value !== self::SENTINEL) {
            return $value;
        }

        if ($dc->activeRecord !== null && \in_array($dc->activeRecord->type, $this->ctaDefaultOff, true)) {
            return '';
        }

        return '1';
    }

    public function beforeSubmitCallback(array $arrValues, DataContainer $dc): array
    {
        $newType = $arrValues['type'] ?? null;

        if (!$newType || !\in_array($newType, $this->ctaDefaultOff, true)) {
            return $arrValues;
        }

        if (!isset($arrValues['isCta'])) {
            $record = Database::getInstance()
                ->prepare("SELECT isCta FROM " . $dc->table . " WHERE id=?")
                ->execute((int) $dc->id);

            if ($record->numRows && $record->isCta === self::SENTINEL) {
                $arrValues['isCta'] = '';
            }
        }

        return $arrValues;
    }
}
