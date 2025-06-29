<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet;

use Neos\Flow\Mvc\ActionRequest;
use SJS\Neos\MCP\Domain\Client\Request\Completion\CompleteRequest\Argument;
use SJS\Neos\MCP\Domain\Client\Request\Completion\CompleteRequest\Ref;
use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Domain\MCP\Completion;
use SJS\Neos\MCP\Domain\MCP\Tool;

#[Flow\Scope("singleton")]
abstract class AbstractFeatureSet implements FeatureSetInterface
{
    protected ActionRequest $actionRequest;

    /**
     * @var array<\SJS\Neos\MCP\Domain\MCP\Tool>
     */
    protected array $tools = [];

    public function addTool(Tool $tool): void
    {
        $this->tools[$tool->name] = $tool;
    }

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

    public function toolsList(): array
    {
        return $this->tools;
    }

    public function toolsCall(string $toolName, array $arguments): mixed
    {
        if (!array_key_exists($toolName, $this->tools)) {
            return null;
        }

        return $this->tools[$toolName]->run($arguments);
    }
}
