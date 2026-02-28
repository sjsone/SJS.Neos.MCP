<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet;

use Neos\ContentRepository\Core\NodeType\NodeType;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Neos\Neos\FrontendRouting\SiteDetection\SiteDetectionResult;
use Psr\Log\LoggerInterface;
use SJS\Neos\MCP\Domain\MCP\Resource;

#[Flow\Scope("singleton")]
class DimensionFeatureSet extends AbstractFeatureSet
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    #[Flow\Inject]
    protected LoggerInterface $logger;


    public function initialize(): void
    {
        // TODO: create tools to list, and show NodeTypes
    }
}
