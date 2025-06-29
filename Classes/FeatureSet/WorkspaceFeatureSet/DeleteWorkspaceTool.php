<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\WorkspaceFeatureSet;

use Neos\ContentRepository\Core\SharedModel\Workspace\WorkspaceName;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Neos\Domain\Service\WorkspaceService;
use Neos\Neos\FrontendRouting\SiteDetection\SiteDetectionResult;
use Psr\Log\LoggerInterface;
use SJS\Neos\MCP\Domain\MCP\Tool;
use SJS\Neos\MCP\JsonSchema\ObjectSchema;
use SJS\Neos\MCP\JsonSchema\StringSchema;


class DeleteWorkspaceTool extends Tool
{
    #[Flow\Inject]
    protected WorkspaceService $workspaceService;

    #[Flow\Inject]
    protected LoggerInterface $logger;

    public function __construct()
    {
        parent::__construct(
            name: 'delete_workspace',
            title: 'Delete Workspace',
            description: 'Delete a workspace',
            inputSchema: new ObjectSchema(properties: [
                'name' => (new StringSchema(description: "technical name of the workspace to be deleted"))->required(),
            ])
        );
    }
    public function run(ActionRequest $actionRequest, array $input)
    {
        $workspaceName = $input["name"];

        $siteDetection = SiteDetectionResult::fromRequest($actionRequest->getHttpRequest());

        $this->workspaceService->deleteWorkspace(
            $siteDetection->contentRepositoryId,
            WorkspaceName::fromString($workspaceName)
        );

        return [
            'status' => 'success',
            'message' => "Workspace '{$workspaceName}' deleted successfully",
        ];
    }
}
