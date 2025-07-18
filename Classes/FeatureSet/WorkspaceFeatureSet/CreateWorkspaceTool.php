<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\WorkspaceFeatureSet;

use Neos\ContentRepository\Core\SharedModel\Workspace\WorkspaceName;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Neos\Domain\Model\WorkspaceDescription;
use Neos\Neos\Domain\Model\WorkspaceRole;
use Neos\Neos\Domain\Model\WorkspaceRoleAssignment;
use Neos\Neos\Domain\Model\WorkspaceRoleAssignments;
use Neos\Neos\Domain\Model\WorkspaceTitle;
use Neos\Neos\Domain\Service\WorkspaceService;
use Neos\Neos\FrontendRouting\SiteDetection\SiteDetectionResult;
use Psr\Log\LoggerInterface;
use SJS\Neos\MCP\Domain\MCP\Tool;
use SJS\Neos\MCP\Domain\MCP\Tool\Annotations;
use SJS\Neos\MCP\JsonSchema\ObjectSchema;
use SJS\Neos\MCP\JsonSchema\StringSchema;


class CreateWorkspaceTool extends Tool
{
    #[Flow\Inject]
    protected WorkspaceService $workspaceService;

    #[Flow\Inject]
    protected LoggerInterface $logger;

    public function __construct()
    {
        // TODO: improve DX for create new Tools because using parent::__construct is a bit awkward
        parent::__construct(
            name: 'create_workspace',
            description: 'Creates a workspace',
            inputSchema: new ObjectSchema(properties: [
                'name' => (new StringSchema(description: "technical name of the new workspace"))->required(),
                'title' => (new StringSchema(description: "visible title of the new workspace"))->required(),
                'description' => (new StringSchema(description: "description of the new workspace")),
            ]),
            annotations: new Annotations(
                title: 'Create Workspace'
            )
        );
    }
    public function run(ActionRequest $actionRequest, array $input)
    {
        $workspaceName = $input["name"];
        $workspaceTitle = $input["title"];
        $workspaceDescription = $input["description"] ?? "";

        $siteDetection = SiteDetectionResult::fromRequest($actionRequest->getHttpRequest());

        $uniqueWorkspaceName = $this->workspaceService->getUniqueWorkspaceName(
            $siteDetection->contentRepositoryId,
            $workspaceName,
        );

        $this->workspaceService->createSharedWorkspace(
            $siteDetection->contentRepositoryId,
            $uniqueWorkspaceName,
            WorkspaceTitle::fromString($workspaceTitle),
            WorkspaceDescription::fromString($workspaceDescription),
            WorkspaceName::forLive(),
            WorkspaceRoleAssignments::create(
                WorkspaceRoleAssignment::createForGroup(
                    'Neos.Neos:AbstractEditor',
                    WorkspaceRole::COLLABORATOR,
                )
            )
        );

        $message = "Workspace created successfully.";
        if (!$uniqueWorkspaceName->equals(WorkspaceName::fromString($workspaceName))) {
            $message .= " But the name has been changed to '{$uniqueWorkspaceName}' because {$workspaceName} already existed.";
        }

        return [
            'status' => 'success',
            'message' => $message,
            'workspace_name' => (string) $uniqueWorkspaceName
        ];
    }
}
