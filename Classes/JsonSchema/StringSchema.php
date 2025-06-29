<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\JsonSchema;

class StringSchema extends AbstractSchema
{
    protected string $type = 'string';

    public function __construct(
        ?string $description = null,
        mixed $default = null,
        private ?int $minLength = null,
        private ?int $maxLength = null,
        private ?string $pattern = null,
        private ?string $format = null,
        private ?array $enum = null
    ) {
        parent::__construct($description, $default);
    }

    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        if ($this->minLength !== null) {
            $data['minLength'] = $this->minLength;
        }
        if ($this->maxLength !== null) {
            $data['maxLength'] = $this->maxLength;
        }
        if ($this->pattern !== null) {
            $data['pattern'] = $this->pattern;
        }
        if ($this->format !== null) {
            $data['format'] = $this->format;
        }
        if ($this->enum !== null) {
            $data['enum'] = $this->enum;
        }
        return $data;
    }
}
