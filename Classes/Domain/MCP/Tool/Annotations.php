<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Domain\MCP\Tool;

class Annotations implements \JsonSerializable
{
    public function __construct(
        // Human-readable title for the tool
        public readonly ?string $title = null,
        // If true, the tool does not modify its environment
        public readonly ?bool $readOnlyHint = null,
        // If true, the tool may perform destructive updates
        public readonly ?bool $destructiveHint = null,
        // If true, repeated calls with same args have no additional effect
        public readonly ?bool $idempotentHint = null,
        // If true, tool interacts with external entities
        public readonly ?bool $openWorldHint = null,
    ) {
    }

    public function jsonSerialize(): mixed
    {
        $data = [];

        if ($this->title !== null) {
            $data['title'] = $this->title;
        }

        if ($this->readOnlyHint !== null) {
            $data['readOnlyHint'] = $this->readOnlyHint;
        }

        if ($this->destructiveHint !== null) {
            $data['destructiveHint'] = $this->destructiveHint;
        }

        if ($this->idempotentHint !== null) {
            $data['idempotentHint'] = $this->idempotentHint;
        }

        if ($this->openWorldHint !== null) {
            $data['openWorldHint'] = $this->openWorldHint;
        }

        return $data;
    }
}
