<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\TestFeatureSet;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Security\Context as SecurityContext;
use Neos\Neos\Domain\Service\UserService;
use SJS\Neos\MCP\Domain\MCP\Tool;
use SJS\Neos\MCP\Domain\MCP\Tool\Annotations;
use SJS\Neos\MCP\Domain\MCP\Tool\Content;
use SJS\Neos\MCP\JsonSchema\ObjectSchema;

class AuthenticatedUserTool extends Tool
{
    #[Flow\Inject]
    protected SecurityContext $securityContext;

    #[Flow\Inject]
    protected UserService $userService;

    public function __construct()
    {
        parent::__construct(
            name: 'authenticated_user',
            description: 'Returns information about the currently authenticated user',
            inputSchema: new ObjectSchema(),
            annotations: new Annotations(
                title: 'Authenticated User',
                readOnlyHint: true
            )
        );
    }

    public function run(ActionRequest $actionRequest, array $input): Content
    {
        $account = $this->securityContext->getAccount();

        if ($account === null) {
            return Content::text('No authenticated user found.');
        }

        $user = $this->userService->getCurrentUser();

        $roles = array_map(
            fn($role) => $role->getIdentifier(),
            array_values($account->getRoles())
        );

        $info = [
            'accountIdentifier' => $account->getAccountIdentifier(),
            'authenticationProviderName' => $account->getAuthenticationProviderName(),
            'roles' => $roles,
            'isActive' => $account->isActive(),
            'creationDate' => $account->getCreationDate()?->format(\DateTimeInterface::ATOM),
            'lastSuccessfulAuthentication' => $account->getLastSuccessfulAuthenticationDate()?->format(\DateTimeInterface::ATOM),
            'failedAuthenticationCount' => $account->getFailedAuthenticationCount(),
        ];

        if ($user !== null) {
            $info['name'] = $user->getLabel();
            $info['isAdministrator'] = $this->userService->currentUserIsAdministrator();
        }

        return Content::structured($info)->addText(json_encode($info, JSON_PRETTY_PRINT));
    }
}
