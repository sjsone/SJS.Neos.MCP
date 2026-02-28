<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\ContentFeatureSet;

use Neos\ContentRepository\Core\DimensionSpace\OriginDimensionSpacePoint;
use Neos\ContentRepository\Core\Feature\NodeModification\Command\SetNodeProperties;
use Neos\ContentRepository\Core\Feature\NodeModification\Dto\PropertyValuesToWrite;
use Neos\ContentRepository\Core\Projection\ContentGraph\VisibilityConstraints;
use Neos\ContentRepository\Core\SharedModel\Node\NodeAddress;
use Neos\ContentRepository\Core\SharedModel\Workspace\WorkspaceName;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Neos\Domain\Service\WorkspaceService;
use Neos\Neos\FrontendRouting\SiteDetection\SiteDetectionResult;
use SJS\Neos\MCP\Domain\MCP\Tool;
use SJS\Neos\MCP\Domain\MCP\Tool\Annotations;
use SJS\Neos\MCP\Domain\MCP\Tool\Content;
use SJS\Neos\MCP\JsonSchema\AnySchema;
use SJS\Neos\MCP\JsonSchema\ObjectSchema;
use SJS\Neos\MCP\JsonSchema\StringSchema;
use Neos\Flow\Annotations as Flow;

class UpdateContentTool extends Tool
{
    #[Flow\Inject]
    protected WorkspaceService $workspaceService;

    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    public function __construct()
    {
        parent::__construct(
            name: 'update_content',
            description: 'Updates properties on an existing content node',
            inputSchema: new ObjectSchema(properties: [
                "node_address" => (new ObjectSchema(
                    description: "The node_address returned from other tools",
                    properties: [
                        "contentRepositoryId" => new StringSchema(),
                        "workspaceName" => new StringSchema(),
                        "dimensionSpacePoint" => new ObjectSchema(),
                        "aggregateId" => new StringSchema()
                    ]
                ))->required(),
                "property_name" => (new StringSchema(description: "The property name to update"))->required(),
                "property_value" => (new AnySchema(description: "The value of the property"))->required(),
            ]),
            annotations: new Annotations(
                title: 'Update Content',
                idempotentHint: true
            )
        );
    }

    public function run(ActionRequest $actionRequest, array $input): Content
    {
        $nodeAddressArray = $input["node_address"];

        $propertyName = $input["property_name"] ?? null;
        if ($propertyName === null) {
            return Content::text('Missing argument "property_name".');
        }

        $propertyValue = $input["property_value"] ?? null;
        if ($propertyValue === null) {
            return Content::text('Missing argument "property_value".');
        }


        $nodeAddress = NodeAddress::fromArray($nodeAddressArray);
        $workspaceName = $nodeAddress->workspaceName;
        if ($workspaceName->equals(WorkspaceName::forLive())) {
            return Content::text('Updating nodes on Live workspace is currently disabled.');
        }

        $httpRequest = $actionRequest->getHttpRequest();
        $contentRepositoryId = SiteDetectionResult::fromRequest(request: $httpRequest)->contentRepositoryId;
        $contentRepository = $this->contentRepositoryRegistry->get(contentRepositoryId: $contentRepositoryId);

        $graph = $contentRepository->getContentGraph(workspaceName: $workspaceName);
        $subGraph = $graph->getSubgraph(dimensionSpacePoint: $nodeAddress->dimensionSpacePoint, visibilityConstraints: VisibilityConstraints::default());

        $node = $subGraph->findNodeById(nodeAggregateId: $nodeAddress->aggregateId);
        if ($node === null) {
            return Content::text('Could not find node.');
        }

        if (!$node->hasProperty($propertyName)) {
            return Content::text('Node does not have property with name: "' . $propertyName . '".');
        }

        if (\is_string($propertyValue)) {
            $propertyValue = str_replace("\\\"", "\"", $propertyValue);
            $propertyValue = str_replace("<\/p>", "</p>", $propertyValue);
        }

        $contentRepository->handle(SetNodeProperties::create(
            workspaceName: $nodeAddress->workspaceName,
            nodeAggregateId: $nodeAddress->aggregateId,
            originDimensionSpacePoint: OriginDimensionSpacePoint::fromDimensionSpacePoint($nodeAddress->dimensionSpacePoint),
            propertyValues: PropertyValuesToWrite::fromArray([
                $propertyName => $propertyValue
            ])
        ));

        return Content::text("Property updated");
    }
}
