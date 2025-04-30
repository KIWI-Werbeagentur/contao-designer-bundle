<?php

namespace Kiwi\Contao\DesignerBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ConfigPluginInterface;
use Kiwi\Contao\CmxBundle\KiwiCmxBundle;
use Kiwi\Contao\DesignerBundle\KiwiDesignerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;

class Plugin implements BundlePluginInterface, ConfigPluginInterface
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

    public function registerContainerConfiguration(LoaderInterface $loader, array $managerConfig): void
    {
        $loader->load(__DIR__ . '/../../config/services.yaml');
    }
}