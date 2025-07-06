<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\MCP;

use Neos\Flow\Mvc\ActionRequest;
use SJS\Neos\MCP\Domain\MCP\Tool\Annotations;
use SJS\Neos\MCP\JsonSchema\AbstractSchema;

abstract class Tool implements \JsonSerializable
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly AbstractSchema $inputSchema,
        public readonly ?AbstractSchema $outputSchema = null,
        public readonly ?Annotations $annotations = null,
    ) {
    }

    public function initializeInput(mixed $input)
    {
        return $input;
    }

    abstract public function run(ActionRequest $actionRequest, array $input);

    public function jsonSerialize(): mixed
    {
        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'inputSchema' => $this->inputSchema,
        ];

        if ($this->outputSchema) {
            $data['outputSchema'] = $this->outputSchema;
        }

        if ($this->annotations) {
            $data['annotations'] = $this->annotations;
        }

        return $data;
    }
}
