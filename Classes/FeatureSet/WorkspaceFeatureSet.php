<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet;

use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Domain\MCP\Tool;
use SJS\Neos\MCP\FeatureSet\WorkspaceFeatureSet\CreateWorkspaceTool;
use SJS\Neos\MCP\JsonSchema\ObjectSchema;

// #[Flow\Scope("singleton")]
class WorkspaceFeatureSet extends AbstractFeatureSet
{
    public function initialize(): void
    {
        $this->addTool(new CreateWorkspaceTool());

    }
}
