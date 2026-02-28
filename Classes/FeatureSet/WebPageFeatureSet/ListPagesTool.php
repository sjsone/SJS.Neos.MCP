<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\WebPageFeatureSet;

use Neos\ContentRepository\Core\NodeType\NodeTypeName;
use Neos\ContentRepository\Core\SharedModel\Node\NodeAddress;
use Neos\ContentRepository\Core\SharedModel\Workspace\WorkspaceName;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Neos\Domain\Model\WorkspaceDescription;
use Neos\Neos\Domain\Model\WorkspaceRole;
use Neos\Neos\Domain\Model\WorkspaceRoleAssignment;
use Neos\Neos\Domain\Model\WorkspaceRoleAssignments;
use Neos\Neos\Domain\Model\WorkspaceTitle;
use Neos\Neos\Domain\Service\WorkspaceService;
use Neos\Neos\FrontendRouting\NodeUriBuilderFactory;
use Neos\Neos\FrontendRouting\SiteDetection\SiteDetectionResult;
use Psr\Log\LoggerInterface;
use SJS\Neos\MCP\Domain\MCP\Tool;
use SJS\Neos\MCP\Domain\MCP\Tool\Annotations;
use SJS\Neos\MCP\Domain\MCP\Tool\Content;
use SJS\Neos\MCP\JsonSchema\ArraySchema;
use SJS\Neos\MCP\JsonSchema\ObjectSchema;
use SJS\Neos\MCP\JsonSchema\StringSchema;


class ListPagesTool extends Tool
{
    #[Flow\Inject]
    protected WorkspaceService $workspaceService;

    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    #[Flow\Inject]
    protected NodeUriBuilderFactory $nodeUriBuilderFactory;

    #[Flow\Inject]
    protected LoggerInterface $logger;

    public function __construct()
    {
        // TODO: improve DX for create new Tools because using parent::__construct is a bit awkward
        parent::__construct(
            name: 'list_pages',
            description: 'Lists all pages of the site',
            inputSchema: new ObjectSchema(properties: [
                'nodeType' => new StringSchema(description: "What NodeTypes should be filtered for", default: "Neos.Demo:Document"),
                // 'dimensions' => new ObjectSchema()...
            ]),
            // outputSchema: new ArraySchema(items: new ObjectSchema(properties: [
            //     'uri' => new StringSchema(),
            //     'aggregateId' => new StringSchema(),
            //     'title' => new StringSchema(),
            //     'uriPathSegment' => new StringSchema(),
            //     'nodeName' => new StringSchema(),
            // ])),
            annotations: new Annotations(
                title: 'List Pages'
            )
        );
    }
    public function run(ActionRequest $actionRequest, array $input)
    {

        $httpRequest = $actionRequest->getHttpRequest();
        $contentRepositoryId = SiteDetectionResult::fromRequest($httpRequest)->contentRepositoryId;
        $contentRepository = $this->contentRepositoryRegistry->get($contentRepositoryId);

        $graph = $contentRepository->getContentGraph(WorkspaceName::forLive());

        $nodeUriBuilder = $this->nodeUriBuilderFactory->forActionRequest($actionRequest);

        $nodeTypeManager = $contentRepository->getNodeTypeManager();

        $filterNodeType = $input['nodeType'] ?? 'Neos.Demo:Document';

        if ($nodeTypeManager->getNodeType($filterNodeType) === null) {
            $this->logger->error("Unknown NodeType: $filterNodeType");
            $filterNodeType = 'Neos.Demo:Document';
        }

        $resources = [];

        $nodeTypes = $nodeTypeManager->getSubNodeTypes(NodeTypeName::fromString($filterNodeType));
        foreach ($nodeTypes as $nodeType) {
            $nodeAggregates = $graph->findNodeAggregatesByType($nodeType->name);

            foreach ($nodeAggregates as $nodeAggregate) {
                foreach ($nodeAggregate->occupiedDimensionSpacePoints as $spacePoint) {
                    $hash = "{$spacePoint->hash}__{$nodeAggregate->nodeAggregateId}";
                    if (\array_key_exists($hash, $resources)) {
                        continue;
                    }

                    $node = $nodeAggregate->getNodeByOccupiedDimensionSpacePoint($spacePoint);

                    $resources[$hash] = [
                        'uri' => $nodeUriBuilder->uriFor(NodeAddress::fromNode($node)),
                        'aggregateId' => $node->aggregateId,
                        'title' => $node->getProperty("title") ?? "",
                        'uriPathSegment' => $node->getProperty("uriPathSegment"),
                        'nodeName' => $node->name,
                    ];
                }
            }
        }

        return Content::structured($resources)->addText(json_encode(array_values($resources)));
    }
}
