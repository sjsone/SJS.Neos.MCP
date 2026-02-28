<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet;

use SJS\Neos\MCP\FeatureSet\WorkspaceFeatureSet\CreateWorkspaceTool;
use SJS\Neos\MCP\FeatureSet\WorkspaceFeatureSet\DeleteWorkspaceTool;
use SJS\Neos\MCP\FeatureSet\WorkspaceFeatureSet\ListWorkspacesTool;

class WorkspaceFeatureSet extends AbstractFeatureSet
{
    public function initialize(): void
    {
        $this->addTool(ListWorkspacesTool::class);
        $this->addTool(CreateWorkspaceTool::class);
        $this->addTool(DeleteWorkspaceTool::class);
    }
}
