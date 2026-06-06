<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Controller\Backend;

use Neos\Fusion\View\FusionView;

trait FusionViewTrait
{


    public function initializeView(\Neos\Flow\Mvc\View\ViewInterface $view): void
    {
        if ($view instanceof FusionView) {
            $view->setFusionPathPattern('resource://SJS.Neos.MCP/Private/Fusion/Module/Root.fusion');
        }
    }
}