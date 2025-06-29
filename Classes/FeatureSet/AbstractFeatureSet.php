<?php
declare(strict_types=1);


namespace SJS\Neos\MCP\FeatureSet;

use Neos\Flow\Mvc\ActionRequest;
use SJS\Neos\MCP\Domain\Client\Request\CompletionCompleteRequest\Argument;
use SJS\Neos\MCP\Domain\Client\Request\CompletionCompleteRequest\Ref;
use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Domain\MCP\Completion;


#[Flow\Scope("singleton")]
abstract class AbstractFeatureSet implements FeatureSetInterface
{
    protected ActionRequest $actionRequest;

    public function setActionRequest(ActionRequest $actionRequest)
    {
        $this->actionRequest = $actionRequest;
    }

    abstract public function initialize(): void;

    /**
     * @return array<\SJS\Neos\MCP\Domain\MCP\Resource>
     */
    public function resourcesList(?string $cursor = null): array
    {
        return [];
    }

    public function resourcesTemplatesList(): array
    {
        return [];
    }

    public function completionComplete(Argument $argument, Ref $ref): ?Completion
    {
        return null;
    }

    /**
     * @return array<\SJS\Neos\MCP\Domain\MCP\Resource>
     */
    public function resourcesRead(string $uri): array
    {
        return [];
    }
}