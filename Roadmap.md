# Roadmap

> This roadmap will be properly incorporated in Issues/a Github Project.

## Until Version 1.0

### Re-Evaluate the Agent concept

It is not a "real" Agent currently so maybe abstract just the Bearer-Token and then lay concepts of "doing things on behalf" on top of it.

In theory "Agents" would be a `Party` themselves with another `Party` (User) linked to it.

### "Real" extensibility using Interfaces

There is no Interface-Based-Injection which makes extending stuff hard or even impossible.

### Improved Authentication and Security

Currently the Agent works on behalf of the Neos _Backend_ User with its full authorization. This is not ideal.

For the Neos Backend User Module has to be able to select what _Roles_ the agent should inherit from the User.

Architecturally the whole System needs a better way of managing the security. Including the AuthenticationProvider, Token, Accounts and Roles.

### Split into a Neos and a Flow Package

The core is essentially the MCP Server with its FeatureSets which is a normal Flow ActionController.

Once the extensibility and security management is solid the package can be split.

The Neos-Package will contain the Neos-Backend Modules and security management.
