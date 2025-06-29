<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\MCP;

use SJS\Neos\MCP\JsonSchema\AbstractSchema;

abstract class Tool implements \JsonSerializable
{
    public function __construct(
        public readonly string $name,
        public readonly string $title,
        public readonly string $description,
        public readonly AbstractSchema $inputSchema,
        public readonly ?AbstractSchema $outputSchema,
        public readonly ?array $annotations,
    ) {
    }

    public function initializeInput(mixed $input)
    {
        return $input;
    }

    abstract public function run(mixed $input);

    public function jsonSerialize(): mixed
    {
        $data = [
            'name' => $this->name,
            'title' => $this->title,
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
