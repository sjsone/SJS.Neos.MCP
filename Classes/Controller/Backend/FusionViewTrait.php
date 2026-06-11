<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Controller\Backend;

use Neos\Fusion\View\FusionView;
use Neos\Flow\Annotations as Flow;

trait FusionViewTrait
{

    #[Flow\InjectConfiguration(path: "modules.view.fusion.pathPatterns")]
    protected array $modulesViewFusionPathsConfiguration;

    public function initializeView(\Neos\Flow\Mvc\View\ViewInterface $view): void
    {
        if ($view instanceof FusionView) {
            $view->setFusionPathPatterns(\array_values($this->modulesViewFusionPathsConfiguration));
        }
    }
}