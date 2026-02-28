<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet;

use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\ObjectManagement\ObjectManager;
use Psr\Log\LoggerInterface;
use SJS\Neos\MCP\Domain\Client\Request\Completion\CompleteRequest\Argument;
use SJS\Neos\MCP\Domain\Client\Request\Completion\CompleteRequest\Ref;
use Neos\Flow\Annotations as Flow;
use SJS\Neos\MCP\Domain\MCP\Completion;
use SJS\Neos\MCP\Domain\MCP\Tool;
use SJS\Neos\MCP\Domain\MCP\Tool\Content;
use SJS\Neos\MCP\JsonSchema\ObjectSchema;

#[Flow\Scope("singleton")]
abstract class AbstractFeatureSet implements FeatureSetInterface
{
    #[Flow\Inject(lazy: false)]
    protected ObjectManager $objectManager;

    #[Flow\Inject]
    protected LoggerInterface $logger;

    protected ActionRequest $actionRequest;

    protected ?string $toolCallPrefix = null;

    protected bool $useToolCallPrefix = true;

    /**
     * @var array<\SJS\Neos\MCP\Domain\MCP\Tool>
     */
    protected array $tools = [];

    /**
     * @param class-string<\SJS\Neos\MCP\Domain\MCP\Tool> $tool
     */
    public function addTool(string $tool): void
    {
        $toolInstance = $this->objectManager->get($tool);
        if (!($toolInstance instanceof Tool)) {
            throw new \Exception("Provided Tool Class '{$tool}' is not an instance of Tool");
        }

        $this->logger->info("Added Tool: " . $toolInstance->name);

        $toolInstance->prefix = $this->generateToolCallPrefix();

        $this->tools[$toolInstance->nameWithPrefix()] = $toolInstance;
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
        // TODO: create resource providing with scheme registration and automatic checks etc.  
        return [];
    }

    public function toolsList(): array
    {
        return $this->tools;
    }

    public function hasTool(string $toolName): bool
    {
        return \array_key_exists($toolName, $this->tools);
    }


    public function toolsCall(string $toolName, array $arguments): mixed
    {
        if (!\array_key_exists($toolName, $this->tools)) {
            throw new \Error("Unknown Tool: $toolName");
        }

        $tool = $this->tools[$toolName];

        try {
            $this->validatedArgumentsForTool(
                tool: $tool,
                arguments: $arguments,
            );
            return $tool->run($this->actionRequest, $arguments);
        } catch (\InvalidArgumentException $e) {
            return Content::text($e->getMessage());
        }
    }

    protected function validatedArgumentsForTool(Tool $tool, array $arguments)
    {
        if ($tool->inputSchema instanceof ObjectSchema) {
            $requiredKeys = array_keys($tool->inputSchema->getRequiredProperties());
            foreach ($requiredKeys as $requiredKey) {
                if (!\array_key_exists($requiredKey, $arguments)) {
                    throw new \InvalidArgumentException("Missing required argument: '$requiredKey'");
                }

                if ($arguments[$requiredKey] === null) {
                    throw new \InvalidArgumentException("Required argument is null: '$requiredKey'");
                }
            }
        }
    }

    protected function generateToolCallPrefix(): ?string
    {
        if ($this->useToolCallPrefix === false) {
            return null;
        }

        if ($this->toolCallPrefix === null) {
            $fqcnParts = explode("\\", \get_class($this));

            $featureSetName = str_replace("FeatureSet", "", end($fqcnParts));

            $featureSetNameParts = preg_split('/(?=[A-Z])/', $featureSetName);
            $featureSetNameParts = array_filter($featureSetNameParts, fn($p) => $p);
            $featureSetNameParts = array_map(fn($p) => strtolower($p), $featureSetNameParts);

            $this->toolCallPrefix = implode("_", $featureSetNameParts);
        }

        return $this->toolCallPrefix;
    }
}
