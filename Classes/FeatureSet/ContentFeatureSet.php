<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet;

use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\FeatureSet\ContentFeatureSet\AddContentTool;
use SJS\Neos\MCP\FeatureSet\ContentFeatureSet\ContentTreeTool;
use SJS\Neos\MCP\FeatureSet\ContentFeatureSet\MoveContentTool;
use SJS\Neos\MCP\FeatureSet\ContentFeatureSet\RemoveContentTool;
use SJS\Neos\MCP\FeatureSet\ContentFeatureSet\UpdateContentTool;

#[Flow\Scope("singleton")]
class ContentFeatureSet extends AbstractFeatureSet
{
    public function initialize(): void
    {
        $this->addTool(ContentTreeTool::class);
        $this->addTool(UpdateContentTool::class);
        $this->addTool(AddContentTool::class);
        $this->addTool(MoveContentTool::class);
        $this->addTool(RemoveContentTool::class);
    }
}
