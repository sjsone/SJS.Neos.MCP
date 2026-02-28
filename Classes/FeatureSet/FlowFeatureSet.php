<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet;

use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\FeatureSet\FlowFeatureSet\ListConfigurationTreeTool;
use SJS\Neos\MCP\FeatureSet\FlowFeatureSet\ListPackagesTool;

#[Flow\Scope("singleton")]
class FlowFeatureSet extends AbstractFeatureSet
{
    public function initialize(): void
    {
        $this->addTool(ListConfigurationTreeTool::class);
        $this->addTool(ListPackagesTool::class);
    }
}
