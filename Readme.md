# SJS.Neos.MCP

Core MCP (Model Context Protocol) server implementation for Neos CMS 9.x.

> [!WARNING]
> This package may work on Neos 8 but this is NOT TESTED!
> It is also under development so things can change

---

## Quick Start

### Neos 9

Install these Feature Sets:

- **Neos** `composer require sjs/neos-mcp-feature-set-neos` [📦 Packagist](https://packagist.org/packages/sjs/neos-mcp-feature-set-neos)
- **ContentRepository** `composer require sjs/neos-mcp-feature-set-cr` [📦 Packagist](https://packagist.org/packages/sjs/neos-mcp-feature-set-cr)
- **Resources** `composer require sjs/neos-mcp-feature-set-resources` [📦 Packagist](https://packagist.org/packages/sjs/neos-mcp-feature-set-resources)

For Testing:

- **Test** `composer require sjs/neos-mcp-feature-set-test` [📦 Packagist](https://packagist.org/packages/sjs/neos-mcp-feature-set-test)

## Configuration

Server instances are defined in `Configuration/Settings.Server.yaml`:

```yaml
SJS:
  Neos:
    MCP:
      server:
        mcp: # <-- This is the default for now
          featureSets:
            myFeatureSet: \Vendor\Site\MCP\FeatureSet\MyFeatureSet
```

Multiple named server instances can coexist alongside `mcp`.

> [!WARNING]
> Currently only `mcp` is used as it is hardcoded for now.

---

## Implementing new Features

### Adding a new FeatureSet

1. Create a class extending `AbstractFeatureSet` with `#[Flow\Scope("singleton")]`.
2. Implement `initialize()` to register tools via `$this->addTool(...)`.
3. Optionally override `resourcesList()`, `resourcesRead()`, `resourcesTemplatesList()`, `completionComplete()`.
4. Register the class in `Configuration/Settings.Server.yaml` under `server.mcp.featureSets`.

#### Tool name prefixes

`AbstractFeatureSet` automatically derives a prefix from the class name (e.g., `WorkspaceFeatureSet` → prefix `workspace`). Tool names are exposed as `{prefix}_{tool_name}`.

---

## Development

### MCP Inspector

```sh
yarn mcp-inspector-cli
```

Launches the [MCP Inspector](https://github.com/modelcontextprotocol/inspector) UI for interactive testing.

### References

- [Model Context Protocol](https://modelcontextprotocol.io/)
- [MCP Inspector](https://github.com/modelcontextprotocol/inspector)
