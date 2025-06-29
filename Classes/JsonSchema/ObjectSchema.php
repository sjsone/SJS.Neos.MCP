<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\JsonSchema;

class ObjectSchema extends AbstractSchema
{
    protected string $type = 'object';
    protected string $schemaVersion = 'http://json-schema.org/draft-07/schema#';

    /**
     * @param string|null $title
     * @param string|null $description
     * @param array<string, AbstractSchema> $properties
     * @param string[] $required
     */
    public function __construct(
        protected ?string $title = null,
        ?string $description = null,
        protected array $properties = [],
        protected array $required = []
    ) {
        parent::__construct($description);

        foreach ($this->properties as $propertyName => $property) {
            if ($property->requiredInObject) {
                $properties[] = $propertyName;
            }
        }
    }

    public function jsonSerialize(): array
    {
        $data = [
            '$schema' => $this->schemaVersion,
            'type' => $this->type,
        ];

        if ($this->title !== null) {
            $data['title'] = $this->title;
        }
        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        if (!empty($this->properties)) {
            $data['properties'] = (object) $this->properties;
        }

        if (!empty($this->required)) {
            $data['required'] = $this->required;
        }

        return $data;
    }
}
