<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\JsonSchema;

class AnySchema extends AbstractSchema
{
    protected string $type = '';

    public function __construct(
        ?string $description = null,
        mixed $default = null,
    ) {
        parent::__construct($description, $default);
    }

    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        unset($data["type"]);
        return $data;
    }
}
