<?php

namespace Kiwi\Contao\DesignerBundle\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;

class CtaListener
{
    public const array CTA_DEFAULT_OFF = [
        'download',
        'downloads',
    ];

    /**
     * The isCta checkbox always shows the stored value, which comes from the column
     * default and not from a per-type setting. Initialize it together with the type
     * change that makes the field visible, so each type gets its own default without
     * ever overriding a value the user has chosen.
     */
    #[AsCallback(table: 'tl_content', target: 'config.onbeforesubmit')]
    public function presetCta(array $arrValues, DataContainer $objDca): array
    {
        $strNewType = $arrValues['type'] ?? null;
        $strOldType = $objDca->getCurrentRecord()['type'] ?? null;
        $arrCtaTypes = $this->getCtaTypes($objDca->table);

        if ($strNewType !== $strOldType
            && \in_array($strNewType, $arrCtaTypes, true)
            && !\in_array($strOldType, $arrCtaTypes, true)
        ) {
            $arrValues['isCta'] = \in_array($strNewType, self::CTA_DEFAULT_OFF, true) ? '' : '1';
        }

        return $arrValues;
    }

    /**
     * Records that predate the per-type CTA defaults carry the bare column default
     * ('1') without ever having been initialized by presetCta. Those are recognizable
     * by an empty ctaDesign: deliberately enabling the checkbox always persists the
     * mandatory ctaDesign select. Present such records with their type default instead
     * of the stale '1', so the next save stores the corrected value — no migration needed.
     */
    #[AsCallback(table: 'tl_content', target: 'fields.isCta.load')]
    public function loadCta(mixed $varValue, DataContainer $objDca): mixed
    {
        $arrRecord = $objDca->getCurrentRecord();

        if ($varValue === '1' && ($arrRecord['ctaDesign'] ?? '') === '') {
            return \in_array($arrRecord['type'] ?? null, self::CTA_DEFAULT_OFF, true) ? '' : '1';
        }

        return $varValue;
    }

    /**
     * @return list<string> the types whose palette contains the isCta field
     */
    private function getCtaTypes(string $strTable): array
    {
        $arrPalettes = array_filter($GLOBALS['TL_DCA'][$strTable]['palettes'] ?? [], 'is_string');

        return array_keys(array_filter($arrPalettes, static fn ($strPalette) => preg_match('/\bisCta\b/', $strPalette)));
    }
}
