<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\JsonSchema;

class ArraySchema extends AbstractSchema
{
    protected string $type = 'array';

    public function __construct(
        ?string $description = null,
        mixed $default = null,
        private ?SchemaComponent $items = null
    ) {
        parent::__construct($description, $default);
    }

    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        if ($this->items !== null) {
            $data['items'] = $this->items;
        }
        return $data;
    }
}
