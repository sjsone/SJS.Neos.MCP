<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\WebPageFeatureSet;

use Neos\ContentRepository\Core\Feature\NodeModification\Command\SetNodeProperties;
use Neos\ContentRepository\Core\Feature\NodeModification\Dto\PropertyValuesToWrite;
use Neos\ContentRepository\Core\SharedModel\Node\NodeAggregateId;
use Neos\ContentRepository\Core\SharedModel\Workspace\WorkspaceName;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Neos\FrontendRouting\SiteDetection\SiteDetectionResult;
use SJS\Neos\MCP\Domain\MCP\Tool;
use SJS\Neos\MCP\Domain\MCP\Tool\Annotations;
use SJS\Neos\MCP\Domain\MCP\Tool\Content;
use SJS\Neos\MCP\JsonSchema\ObjectSchema;
use SJS\Neos\MCP\JsonSchema\StringSchema;

class UpdatePageTitleTool extends Tool
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    public function __construct()
    {
        parent::__construct(
            name: 'update_page_title',
            description: 'Updates the title of a page node',
            inputSchema: new ObjectSchema(
                properties: [
                    'nodeAggregateId' => new StringSchema(description: 'Aggregate ID of the node (as returned by list_pages)'),
                    'title' => new StringSchema(description: 'New title value'),
                    'workspace' => new StringSchema(description: "Workspace name, defaults to 'live'"),
                ],
                required: ['nodeAggregateId', 'title']
            ),
            annotations: new Annotations(
                title: 'Update Page Title',
                idempotentHint: true
            )
        );
    }

    public function run(ActionRequest $actionRequest, array $input): Content
    {
        $httpRequest = $actionRequest->getHttpRequest();
        $contentRepositoryId = SiteDetectionResult::fromRequest($httpRequest)->contentRepositoryId;
        $contentRepository = $this->contentRepositoryRegistry->get($contentRepositoryId);

        $workspaceName = WorkspaceName::fromString($input['workspace'] ?? 'live');
        $graph = $contentRepository->getContentGraph($workspaceName);

        $nodeAggregate = $graph->findNodeAggregateById(
            NodeAggregateId::fromString($input['nodeAggregateId'])
        );

        if ($nodeAggregate === null) {
            return Content::text("Node aggregate '{$input['nodeAggregateId']}' not found.");
        }

        foreach ($nodeAggregate->occupiedDimensionSpacePoints as $spacePoint) {
            $node = $nodeAggregate->getNodeByOccupiedDimensionSpacePoint($spacePoint);
            $contentRepository->handle(
                SetNodeProperties::create(
                    $workspaceName,
                    $nodeAggregate->nodeAggregateId,
                    $node->originDimensionSpacePoint,
                    PropertyValuesToWrite::fromArray(['title' => $input['title']])
                )
            );
        }

        return Content::text('Title updated successfully.');
    }
}
