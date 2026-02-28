<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet;

use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\FeatureSet\NodeTypeFeatureSet\GetNodeTypeTool;
use SJS\Neos\MCP\FeatureSet\NodeTypeFeatureSet\ListNodeTypesTool;

#[Flow\Scope("singleton")]
class NodeTypeFeatureSet extends AbstractFeatureSet
{
    public function initialize(): void
    {
        $this->addTool(ListNodeTypesTool::class);
        $this->addTool(GetNodeTypeTool::class);
    }
}
