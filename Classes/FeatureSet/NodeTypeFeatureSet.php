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
class NodeTypeFeatureSet extends AbstractFeatureSet
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    #[Flow\Inject]
    protected LoggerInterface $logger;


    public function initialize(): void
    {
    }

    /**
     * @return array<\SJS\Neos\MCP\Domain\MCP\Resource>
     */
    public function resourcesList(?string $cursor = null): array
    {
        $httpRequest = $this->actionRequest->getHttpRequest();
        $contentRepositoryId = SiteDetectionResult::fromRequest($httpRequest)->contentRepositoryId;
        $contentRepository = $this->contentRepositoryRegistry->get($contentRepositoryId);

        $resources = [];

        $nodeTypes = $contentRepository->getNodeTypeManager()->getNodeTypes(true);
        foreach ($nodeTypes as $nodeType) {
            $resources[] = $this->buildResourceForListFromNodeType($nodeType);
        }

        return array_values($resources);
    }

    protected function buildResourceForListFromNodeType(NodeType $nodeType): Resource
    {
        return Resource::createForListing(
            uri: $this->buildNodeTypeUri($nodeType),
            name: (string) $nodeType->name,
            title: $nodeType->getLabel(),
            description: $nodeType->getConfiguration("description") ?? "",
            mimeType: "application/x-yaml"
        );
    }

    /**
     * @return array<\SJS\Neos\MCP\Domain\MCP\Resource>
     */
    public function resourcesRead(string $uri): array
    {
        $scheme = parse_url($uri, PHP_URL_SCHEME);
        if ($scheme !== "nodetypes") {
            return [];
        }

        $potentialNodeTypeName = parse_url($uri, PHP_URL_PATH);
        if (str_starts_with($potentialNodeTypeName, "/")) {
            $potentialNodeTypeName = substr($potentialNodeTypeName, 1);
        }

        $httpRequest = $this->actionRequest->getHttpRequest();
        $contentRepositoryId = SiteDetectionResult::fromRequest($httpRequest)->contentRepositoryId;
        $contentRepository = $this->contentRepositoryRegistry->get($contentRepositoryId);

        $nodeType = $contentRepository->getNodeTypeManager()->getNodeType($potentialNodeTypeName);

        return [
            Resource::createTextResource(
                uri: $this->buildNodeTypeUri($nodeType),
                name: (string) $nodeType->name,
                title: $nodeType->getLabel(),
                description: $nodeType->getConfiguration("description") ?? "",
                mimeType: "application/json",
                text: json_encode($nodeType->getFullConfiguration(), JSON_UNESCAPED_SLASHES)
            )
        ];
    }

    protected function buildNodeTypeUri(NodeType $nodeType): string
    {
        $name = (string) $nodeType->name;
        [$namespace, $_] = explode(":", $name, 2);

        return "nodetypes://$namespace/$name";
    }
}
