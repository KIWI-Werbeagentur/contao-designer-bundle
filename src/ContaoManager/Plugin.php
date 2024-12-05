<?php

namespace Kiwi\Contao\DesignerBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Kiwi\Contao\CmxBundle\KiwiCmxBundle;
use Kiwi\Contao\DesignerBundle\KiwiDesignerBundle;

class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(KiwiDesignerBundle::class)
                ->setLoadAfter([
                    ContaoCoreBundle::class,
                    KiwiCmxBundle::class,
                ]),
        ];
    }
}