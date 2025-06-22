<?php
declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet;

interface FeatureSetInterface
{
    /**
     * @param null|string $cursor
     * @return array<\SJS\Neos\MCP\Domain\MCP\Resource>
     */
    public function resourcesList(?string $cursor = null): array;
}