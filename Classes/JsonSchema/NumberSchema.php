<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\JsonSchema;

class NumberSchema extends AbstractSchema
{
    protected string $type = 'number';

    public function __construct(
        ?string $description = null,
        mixed $default = null,
        private ?float $minimum = null,
        private ?float $maximum = null
    ) {
        parent::__construct($description, $default);
    }

    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        if ($this->minimum !== null) {
            $data['minimum'] = $this->minimum;
        }
        if ($this->maximum !== null) {
            $data['maximum'] = $this->maximum;
        }
        return $data;
    }
}
