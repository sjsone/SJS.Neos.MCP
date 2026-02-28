<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet;

use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Neos\Neos\FrontendRouting\NodeUriBuilder;
use Neos\Neos\FrontendRouting\NodeUriBuilderFactory;
use SJS\Neos\MCP\FeatureSet\WebPageFeatureSet\ListPagesTool;

#[Flow\Scope("singleton")]
class WebPageFeatureSet extends AbstractFeatureSet
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;




    public function initialize(): void
    {
        $this->addTool(ListPagesTool::class);
    }
}
