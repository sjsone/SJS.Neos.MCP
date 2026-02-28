<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet;

use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\FeatureSet\TestFeatureSet\AuthenticatedUserTool;
use SJS\Neos\MCP\FeatureSet\TestFeatureSet\PingTool;
use SJS\Neos\MCP\FeatureSet\TestFeatureSet\ServerInfoTool;

#[Flow\Scope("singleton")]
class TestFeatureSet extends AbstractFeatureSet
{
    public function initialize(): void
    {
        $this->addTool(PingTool::class);
        $this->addTool(AuthenticatedUserTool::class);
        $this->addTool(ServerInfoTool::class);
    }
}
