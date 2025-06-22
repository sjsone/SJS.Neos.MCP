<?php
declare(strict_types=1);


namespace SJS\Neos\MCP\FeatureSet;

use Neos\ContentRepository\Core\NodeType\NodeTypeName;
use Neos\ContentRepository\Core\SharedModel\Node\NodeAddress;
use Neos\ContentRepository\Core\SharedModel\Workspace\WorkspaceName;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Neos\Neos\FrontendRouting\NodeUriBuilder;
use Neos\Neos\FrontendRouting\NodeUriBuilderFactory;
use Neos\Neos\FrontendRouting\Options;
use Neos\Neos\FrontendRouting\SiteDetection\SiteDetectionResult;
use SJS\Neos\MCP\Domain\MCP\Resource;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;


#[Flow\Scope("singleton")]
class WebPageFeatureSet extends AbstractFeatureSet
{

    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    #[Flow\Inject]
    protected NodeUriBuilderFactory $nodeUriBuilderFactory;
    private NodeUriBuilder $nodeUriBuilder;

    public function initialize(): void
    {
        $this->nodeUriBuilder = $this->nodeUriBuilderFactory->forActionRequest($this->actionRequest);
    }

    /**
     * @return array<\SJS\Neos\MCP\Domain\MCP\Resource>
     */
    public function resourcesList(?string $cursor = null): array
    {
        $httpRequest = $this->actionRequest->getHttpRequest();
        $contentRepositoryId = SiteDetectionResult::fromRequest($httpRequest)->contentRepositoryId;
        $contentRepository = $this->contentRepositoryRegistry->get($contentRepositoryId);

        $graph = $contentRepository->getContentGraph(WorkspaceName::forLive());

        $resources = [];
        $nodeTypes = $contentRepository->getNodeTypeManager()->getSubNodeTypes(NodeTypeName::fromString('Neos.Demo:Document'));
        foreach ($nodeTypes as $nodeType) {
            $nodeAggregates = $graph->findNodeAggregatesByType($nodeType->name);

            foreach ($nodeAggregates as $nodeAggregate) {
                foreach ($nodeAggregate->occupiedDimensionSpacePoints as $spacePoint) {
                    $hash = "{$spacePoint->hash}__{$nodeAggregate->nodeAggregateId}";
                    if (array_key_exists($hash, $resources)) {
                        continue;
                    }

                    $node = $nodeAggregate->getNodeByOccupiedDimensionSpacePoint($spacePoint);
                    if (!$this->isNodeAvailableForMCP($node)) {
                        continue;
                    }

                    $resources[$hash] = $this->buildResourceForListFromNode($node);
                }
            }
        }

        return array_values($resources);
    }

    public function resourcesTemplatesList(): array
    {
        // todo: completion/complete for templates
        return [
            [
                'uriTemplate' => "http:\/\/neos9.ddev.site\/{language}\/",
                "name" => "Languages",
                "title" => "🌍 Languages",
                "description" => "Access files in the project directory",
                "mimeType" => "text\/html"
            ]
        ];
    }

    protected function isNodeAvailableForMCP(Node $node): bool
    {
        return true;
    }

    protected function buildResourceForListFromNode(Node $node): Resource
    {
        return new Resource(
            (string) $this->nodeUriBuilder->uriFor(NodeAddress::fromNode($node), Options::createForceAbsolute()),
            $node->getProperty('title') ?? $node->getProperty("uriPathSegment"),
            $node->getProperty('title') ?? "",
            $node->getProperty('metaDescription') ?? "",
            "text/html"
        );
    }
}
