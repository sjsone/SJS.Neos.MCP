<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\WorkspaceFeatureSet;

use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Neos\Domain\Repository\WorkspaceMetadataAndRoleRepository;
use Neos\Neos\FrontendRouting\SiteDetection\SiteDetectionResult;
use SJS\Neos\MCP\Domain\MCP\Tool;
use SJS\Neos\MCP\Domain\MCP\Tool\Annotations;
use SJS\Neos\MCP\Domain\MCP\Tool\Content;
use SJS\Neos\MCP\JsonSchema\ObjectSchema;


class ListWorkspacesTool extends Tool
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    #[Flow\Inject]
    protected WorkspaceMetadataAndRoleRepository $workspaceMetadataAndRoleRepository;

    public function __construct()
    {
        parent::__construct(
            name: 'list_workspaces',
            description: 'Lists all workspaces with metadata',
            inputSchema: new ObjectSchema(),
            annotations: new Annotations(
                title: 'List Workspaces',
                readOnlyHint: true
            )
        );
    }

    public function run(ActionRequest $actionRequest, array $input): Content
    {
        $siteDetection = SiteDetectionResult::fromRequest($actionRequest->getHttpRequest());
        $cr = $this->contentRepositoryRegistry->get($siteDetection->contentRepositoryId);

        $workspaces = [];
        foreach ($cr->findWorkspaces() as $workspace) {
            $workspaceMetadata = $this->workspaceMetadataAndRoleRepository->loadWorkspaceMetadata(
                $siteDetection->contentRepositoryId,
                $workspace->workspaceName
            );

            $workspaces[(string) $workspace->workspaceName] = [
                'title' => $workspaceMetadata->title->value,
                'description' => $workspaceMetadata->description->value,
                'classification' => $workspaceMetadata->classification->value,
                'ownerUserId' => $workspaceMetadata->ownerUserId,
            ];
        }

        return Content::structured($workspaces)->addText(json_encode($workspaces));
    }
}
