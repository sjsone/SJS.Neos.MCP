<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet;

use Generator;
use Neos\Flow\Annotations as Flow;
use Neos\Media\Domain\Model\Asset;
use Neos\Media\Domain\Model\AssetCollection;
use Neos\Media\Domain\Repository\AssetCollectionRepository;
use Neos\Media\Domain\Repository\AssetRepository;
use Neos\Media\Domain\Repository\TagRepository;
use Psr\Log\LoggerInterface;
use SJS\Neos\MCP\Domain\Client\Request\Completion\CompleteRequest\Argument;
use SJS\Neos\MCP\Domain\Client\Request\Completion\CompleteRequest\Ref;
use SJS\Neos\MCP\Domain\MCP\Completion;
use SJS\Neos\MCP\Domain\MCP\Resource;

#[Flow\Scope("singleton")]
class AssetFeatureSet extends AbstractFeatureSet
{
    #[Flow\Inject]
    protected AssetRepository $assetRepository;

    #[Flow\Inject]
    protected AssetCollectionRepository $assetCollectionRepository;

    #[Flow\Inject]
    protected TagRepository $tagRepository;

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
        $resources = [];

        foreach ($this->assetRepository->findAll() as $asset) {
            /** @var Asset $asset */

            foreach ($this->createAllResourcesForAsset($asset) as $resource) {
                $resources[] = $resource;
            }
        }

        return $resources;
    }

    public function resourcesTemplatesList(): array
    {
        return [
            [
                'uriTemplate' => "resource://byTag/{tag}/",
                "name" => "resourcesByTag",
                "title" => "🌍 Resources by Tag",
                "description" => "Access resource by Tag"
            ],
            [
                'uriTemplate' => "resource://byCollection/{collection}/",
                "name" => "resourcesByCollection",
                "title" => "🌍 Resources by Collection",
                "description" => "Access resource by Collection"
            ],
        ];
    }

    public function completionComplete(Argument $argument, Ref $ref): ?Completion
    {
        $this->logger->info("completionComplete::");
        $this->logger->info("  Argument: {$argument->name}:{$argument->value}");
        $this->logger->info("  Ref: Type: {$ref->type} Uri: {$ref->uri}");

        $templates = $this->resourcesTemplatesList();
        foreach ($templates as $template) {
            if ($template['uriTemplate'] === $ref->uri) {

                if ($argument->name === "collection") {
                    $values = [];

                    foreach ($this->assetCollectionRepository->findAll() as $collection) {
                        /** @var AssetCollection $collection */

                        $values[] = $collection->getTitle();
                    }
                    return new Completion($values, count($values), false);
                }

                if ($argument->name === "tag") {
                    $values = [];
                    foreach ($this->tagRepository->findAll() as $tag) {
                        /** @var \Neos\Media\Domain\Model\Tag $ag */

                        $label = $tag->getLabel();
                        if (str_contains($label, $argument->value)) {
                            $values[] = $tag->getLabel();
                        }
                    }
                    return new Completion($values, count($values), false);
                }
            }
        }
        return null;
    }

    protected function createAllResourcesForAsset(Asset $asset): Generator
    {
        $assetResource = $asset->getResource();

        $fileSize = $assetResource->getFileSize();
        if (is_string($fileSize)) {
            $fileSize = intval($fileSize);
        }

        yield new Resource(
            "resource://byId/{$asset->getIdentifier()}",
            $assetResource->getFilename(),
            $asset->getTitle(),
            $asset->getCaption(),
            $asset->getMediaType(),
            $fileSize,
        );
    }

    /**
     * @return array<\SJS\Neos\MCP\Domain\MCP\Resource>
     */
    public function resourcesRead(string $uri): array
    {
        $scheme = parse_url($uri, PHP_URL_SCHEME);
        if ($scheme !== "resource") {
            return [];
        }

        $this->logger->info("URI: $uri");

        $resolved = $this->resolveUriAgainstTemplates($uri);
        if ($resolved === null) {
            return [];
        }

        return [];
    }

    protected function resolveUriAgainstTemplates(string $uri): ?array
    {
        $templates = $this->resourcesTemplatesList();
        foreach ($templates as $template) {
            $match = $this->matchUriToTemplate($template['uriTemplate'], $uri);
            if ($match === null) {
                continue;
            }

            return [
                'template' => $template,
                'parameters' => $match
            ];
        }
        return null;
    }

    protected function matchUriToTemplate(string $uriTemplate, string $uri): ?array
    {
        $templatePath = parse_url($uriTemplate, PHP_URL_PATH);
        $uriPath = parse_url($uri, PHP_URL_PATH);

        $templateSegments = explode('/', trim($templatePath, '/'));
        $uriSegments = explode('/', trim($uriPath, '/'));

        if (count($templateSegments) !== count($uriSegments)) {
            return null;
        }

        $params = [];

        foreach ($templateSegments as $index => $segment) {
            if (preg_match('/\{([^}]+)\}/', $segment, $matches)) {
                $paramName = $matches[1];
                $params[$paramName] = $uriSegments[$index];
            } elseif ($segment !== $uriSegments[$index]) {
                return null;
            }
        }

        return $params;
    }
}
