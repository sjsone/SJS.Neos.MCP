<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Controller\Backend;

use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Security\Context;
use Neos\Flow\Security\Policy\PolicyService;
use Neos\Fusion\View\FusionView;
use Neos\Neos\Domain\Repository\UserRepository;
use Neos\Party\Domain\Model\AbstractParty;
use Neos\Flow\Annotations as Flow;
use Neos\Party\Domain\Repository\PartyRepository;
use SJS\Neos\MCP\Domain\Model\AgentData;
use SJS\Neos\MCP\Domain\Repository\AgentDataRepository;


class AgentModuleController extends ActionController
{

    protected $defaultViewObjectName = FusionView::class;

    #[Flow\Inject()]
    protected Context $securityContext;

    #[Flow\Inject()]
    protected PartyRepository $partyRepository;

    #[Flow\Inject()]
    protected AgentDataRepository $agentDataRepository;

    #[Flow\Inject()]
    protected UserRepository $userRepository;

    #[Flow\Inject]
    protected PolicyService $policyService;

    public function initializeView(\Neos\Flow\Mvc\View\ViewInterface $view)
    {
        if ($view instanceof FusionView) {
            $view->setFusionPathPattern("resource://SJS.Neos.MCP/Private/Fusion/Module/Root.fusion");
        }
    }

    public function indexAction(): void
    {
        $agentsByParty = [];
        foreach ($this->getAuthenticatedParties() as $party) {
            $agentsByParty[] = [
                "party" => $party,
                "agents" => $this->agentDataRepository->findByParty($party),
            ];
        }

        $this->view->assign("agentsByParty", $agentsByParty);
    }

    public function editAction(AgentData $agent): void
    {
        // TODO: sanity check if agent can be edited by current user

        $this->view->assign('agent', $agent);
        $this->view->assign('accounts', $this->getAccounts());
        $this->view->assign('roles', $this->policyService->getRoles());
        $this->view->assign('allowedRoles', array_fill_keys($agent->getOnlyAllowedRoleIdentifiers(), true));
    }

    public function newAction(): void
    {
        $this->view->assign('accounts', $this->getAccounts());
        $this->view->assign('roles', $this->policyService->getRoles());
    }

    public function createAction(
        string $name,
        ?string $accountIdentifier = null,
        array $onlyAllowedRoleIdentifiers = []
    ): void {
        // TODO: do not just use the first one but let the user decide in the newAction for what party it should be added
        $party = $this->getAuthenticatedParties()[0];

        $agent = new AgentData();
        $agent->setParty($party);
        $agent->setName($name);
        $agent->setToken(bin2hex(random_bytes(32)));

        if ($accountIdentifier !== null) {
            foreach ($this->getAuthenticatedAccounts() as $account) {
                if ($account->getAccountIdentifier() === $accountIdentifier) {
                    $agent->setAccount($account);
                    break;
                }
            }
        }

        $agent->setOnlyAllowedRoleIdentifiers($onlyAllowedRoleIdentifiers);
        $this->agentDataRepository->add($agent);

        // TODO: flash message after successful creation
        $this->redirect('index');
    }

    public function updateAction(
        AgentData $agent,
        string $name,
        ?string $account = null,
        array $onlyAllowedRoleIdentifiers = []
    ): void {
        $agent->setName($name);

        $selectedAccount = null;
        if ($account !== null) {
            foreach ($this->getAuthenticatedAccounts() as $candidate) {
                if ($candidate->getAccountIdentifier() === $account) {
                    $selectedAccount = $candidate;
                    break;
                }
            }
        }
        $agent->setAccount($selectedAccount);
        $agent->setOnlyAllowedRoleIdentifiers($onlyAllowedRoleIdentifiers);

        $this->agentDataRepository->update($agent);

        // TODO: flash message after successful update
        $this->redirect('index');
    }

    public function deleteAction(AgentData $agent): void
    {
        $this->agentDataRepository->remove($agent);

        // TODO: flash message after successful deletion
        $this->redirect('index');
    }

    protected function getAccounts(): array
    {
        $accounts = [];

        $parties = $this->getAuthenticatedParties();
        foreach ($parties as $party) {
            $accounts = [...$accounts, ...$party->getAccounts()];
        }

        return $accounts;
    }

    protected function getAuthenticatedAccounts(): array
    {
        $accounts = [];
        foreach ($this->securityContext->getAuthenticationTokens() as $token) {
            if ($token->isAuthenticated() === false) {
                continue;
            }
            $account = $token->getAccount();
            if ($account === null) {
                continue;
            }
            if (!\in_array($account, $accounts)) {
                $accounts[] = $account;
            }
        }
        return $accounts;
    }

    /**
     * @return AbstractParty[]
     */
    protected function getAuthenticatedParties(): array
    {
        $parties = [];

        foreach ($this->securityContext->getAuthenticationTokens() as $token) {
            if ($token->isAuthenticated() === false) {
                continue;
            }

            $account = $token->getAccount();
            if ($account === null) {
                continue;
            }

            $party = $this->partyRepository->findOneHavingAccount($account);
            if ($party === null) {
                continue;
            }

            if (!\in_array($party, $parties)) {
                $parties[] = $party;
            }
        }

        return $parties;
    }
}