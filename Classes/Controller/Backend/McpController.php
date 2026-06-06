<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Controller\Backend;

use Neos\Flow\Annotations as Flow;
use Neos\Fusion\View\FusionView;
use Neos\Neos\Controller\Module\AbstractModuleController;

class McpController extends AbstractModuleController
{
    use FusionViewTrait;

    protected $defaultViewObjectName = FusionView::class;
}