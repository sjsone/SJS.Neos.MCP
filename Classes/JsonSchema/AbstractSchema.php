<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\JsonSchema;

abstract class AbstractSchema implements SchemaComponent
{
    protected string $type;
    protected ?string $description;
    protected mixed $default;
    protected bool $requiredInObject = false;

    public function __construct(?string $description = null, mixed $default = null)
    {
        $this->description = $description;
        $this->default = $default;
    }

    public function required()
    {
        $this->requiredInObject = true;
        return $this;
    }

    public function jsonSerialize(): array
    {
        $data = ['type' => $this->type];

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }
        if ($this->default !== null) {
            $data['default'] = $this->default;
        }

        return $data;
    }
}
