<?php

namespace Kiwi\Contao\DesignerBundle\DataContainer;

use Contao\DataContainer;
use Contao\Database;

class CtaListener
{
    public const array CTA_DEFAULT_OFF = [
        'download',
        'downloads',
    ];

    public const string UNSET_VALUE = '3';

    public function loadCallback(mixed $value, DataContainer $dc): mixed
    {
        if ($value !== self::UNSET_VALUE) {
            return $value;
        }

        if ($dc->activeRecord !== null && \in_array($dc->activeRecord->type, self::CTA_DEFAULT_OFF, true)) {
            return '';
        }

        return '1';
    }

    public function beforeSubmitCallback(array $arrValues, DataContainer $dc): array
    {
        $newType = $arrValues['type'] ?? null;

        if (!$newType || !\in_array($newType, self::CTA_DEFAULT_OFF, true)) {
            return $arrValues;
        }

        if (!isset($arrValues['isCta'])) {
            $record = Database::getInstance()
                ->prepare("SELECT isCta FROM " . $dc->table . " WHERE id=?")
                ->execute((int) $dc->id);

            if ($record->numRows && $record->isCta === self::UNSET_VALUE) {
                $arrValues['isCta'] = '';
            }
        }

        return $arrValues;
    }
}
