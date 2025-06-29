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
use Psr\Log\LoggerInterface;
use SJS\Neos\MCP\Domain\Client\Request\Completion\CompleteRequest\Argument;
use SJS\Neos\MCP\Domain\Client\Request\Completion\CompleteRequest\Ref;
use SJS\Neos\MCP\Domain\MCP\Completion;
use SJS\Neos\MCP\Domain\MCP\Resource;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use GuzzleHttp\Psr7\Request;

#[Flow\Scope("singleton")]
class WebPageFeatureSet extends AbstractFeatureSet
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    #[Flow\Inject]
    protected NodeUriBuilderFactory $nodeUriBuilderFactory;

    #[Flow\Inject]
    protected LoggerInterface $logger;

    protected NodeUriBuilder $nodeUriBuilder;

    protected \Psr\Http\Client\ClientInterface $client;

    public function initialize(): void
    {
        $this->nodeUriBuilder = $this->nodeUriBuilderFactory->forActionRequest($this->actionRequest);
        $this->initializeHttpClient();
    }

    protected function initializeHttpClient()
    {
        $this->client = new \GuzzleHttp\Client();
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
        return [
            [
                "uriTemplate" => "http:\/\/neos9.ddev.site\/{language}\/",
                "name" => "Languages",
                "title" => "🌍 Languages",
                "description" => "Access files in the project directory",
                "mimeType" => "text\/html"
            ]
        ];
    }


    public function completionComplete(Argument $argument, Ref $ref): ?Completion
    {
        $templates = $this->resourcesTemplatesList();
        foreach ($templates as $template) {
            if ($template['uriTemplate'] === $ref->uri) {
                return new Completion(["", "de", "uk"], 3, false);
            }
        }
        return null;
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

    /**
     * @return array<\SJS\Neos\MCP\Domain\MCP\Resource>
     */
    public function resourcesRead(string $uri): array
    {
        $scheme = parse_url($uri, PHP_URL_SCHEME);

        $this->logger->info("WebPageFeatureSet: resourcesRead: $scheme");
        if ($scheme !== "http" && $scheme !== "https") {
            return [];
        }

        $request = new Request('GET', $uri);
        $response = $this->client->sendRequest($request);
        $content = $response->getBody()->getContents();

        $size = strlen($content);
        $this->logger->info("Got content with size of {$size}");
        $this->logger->info("Got content: {$content}");

        return [
            new Resource(
                $uri,
                "",
                null,
                null,
                'text/html',
                $size,
                $content
            )
        ];
    }
}
