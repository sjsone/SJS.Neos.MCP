<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\MCP;

use Neos\Flow\Mvc\ActionRequest;
use SJS\Neos\MCP\Domain\MCP\Tool\Annotations;
use SJS\Neos\MCP\JsonSchema\AbstractSchema;

abstract class Tool implements \JsonSerializable
{
    public ?string $prefix = null;

    // TODO: use get hook instead of method
    public function nameWithPrefix(): string
    {
        return ($this->prefix !== null ? "{$this->prefix}_" : "") . $this->name;
    }

    // TODO: improve DX for create new Tools because using parent::__construct is a bit awkward

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
            'name' => $this->nameWithPrefix(),
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
