<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Controller\Backend;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Configuration\ConfigurationManager;
use Neos\Fusion\View\FusionView;
use Neos\Neos\Controller\Module\AbstractModuleController;

class BaseMcpModuleController extends AbstractModuleController
{
    use FusionViewTrait;

    protected $defaultViewObjectName = FusionView::class;

    #[Flow\Inject]
    protected ConfigurationManager $configurationManager;

    public function indexAction(): void
    {
        $settings = $this->configurationManager->getConfiguration(
            ConfigurationManager::CONFIGURATION_TYPE_SETTINGS,
            'SJS.Flow.MCP'
        );
        $serverSettings = $settings['server'] ?? [];

        $servers = [];
        $featureSets = [];
        foreach ($serverSettings as $serverName => $serverConfig) {
            if (!\is_array($serverConfig)) {
                continue;
            }
            $serverFeatureSets = [];
            foreach ($serverConfig['featureSets'] ?? [] as $key => $fs) {
                $impl = \is_array($fs) ? ($fs['implementation'] ?? $key) : $fs;
                $serverFeatureSets[$key] = $impl;
                $featureSets[$key] = $impl;
            }
            $servers[$serverName] = [
                'name' => $serverName,
                'featureSets' => $serverFeatureSets,
            ];
        }

        $this->view->assign('servers', $servers);
        $this->view->assign('featureSets', $featureSets);
    }
}
