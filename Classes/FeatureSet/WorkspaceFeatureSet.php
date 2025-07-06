<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet;

use Neos\ContentRepository\Core\SharedModel\Workspace\WorkspaceName;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Neos\Neos\Domain\Repository\WorkspaceMetadataAndRoleRepository;
use Neos\Neos\FrontendRouting\SiteDetection\SiteDetectionResult;
use Psr\Log\LoggerInterface;
use SJS\Neos\MCP\Domain\MCP\Resource;
use SJS\Neos\MCP\FeatureSet\WorkspaceFeatureSet\CreateWorkspaceTool;
use SJS\Neos\MCP\FeatureSet\WorkspaceFeatureSet\DeleteWorkspaceTool;

class WorkspaceFeatureSet extends AbstractFeatureSet
{
    #[Flow\Inject]
    protected LoggerInterface $logger;

    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    #[Flow\Inject]
    protected WorkspaceMetadataAndRoleRepository $workspaceMetadataAndRoleRepository;

    public function initialize(): void
    {
        $this->addTool(CreateWorkspaceTool::class);
        $this->addTool(DeleteWorkspaceTool::class);
    }

    /**
     * @return array<\SJS\Neos\MCP\Domain\MCP\Resource>
     */
    public function resourcesList(string|null $cursor = null): array
    {

        $siteDetection = SiteDetectionResult::fromRequest($this->actionRequest->getHttpRequest());

        $cr = $this->contentRepositoryRegistry->get($siteDetection->contentRepositoryId);

        $workspaceResources = [];
        foreach ($cr->findWorkspaces() as $workspace) {
            $workspaceMetadata = $this->workspaceMetadataAndRoleRepository->loadWorkspaceMetadata(
                $siteDetection->contentRepositoryId,
                $workspace->workspaceName
            );

            $workspaceResources[] = Resource::createForListing(
                uri: "workspace://{$workspace->workspaceName}",
                name: (string) $workspace->workspaceName,
                title: $workspaceMetadata->title->value,
                description: $workspaceMetadata->description->value
            );
        }

        return $workspaceResources;
    }

    public function resourcesRead(string $uri): array
    {
        $workspaceName = parse_url($uri, PHP_URL_HOST);

        $siteDetection = SiteDetectionResult::fromRequest($this->actionRequest->getHttpRequest());

        $workspace = $this->contentRepositoryRegistry
            ->get($siteDetection->contentRepositoryId)
            ->findWorkspaceByName(WorkspaceName::fromString($workspaceName));

        $workspaceMetadata = $this->workspaceMetadataAndRoleRepository->loadWorkspaceMetadata(
            $siteDetection->contentRepositoryId,
            $workspace->workspaceName
        );

        return [
            Resource::createTextResource(
                uri: "workspace://{$workspace->workspaceName}",
                name: (string) $workspace->workspaceName,
                title: $workspaceMetadata->title->value,
                description: $workspaceMetadata->description->value,
                mimeType: "application/json",
                text: json_encode([
                    "classification" => $workspaceMetadata->classification->value,
                    "ownerUserId" => $workspaceMetadata->ownerUserId
                ])
            )
        ];
    }
}
