<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Controller\Backend;

use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Security\Account;
use Neos\Flow\Security\Context;
use Neos\Flow\Security\Policy\PolicyService;
use Neos\Fusion\View\FusionView;
use Neos\Neos\Domain\Repository\UserRepository;
use Neos\Party\Domain\Model\AbstractParty;
use Neos\Flow\Annotations as Flow;
use Neos\Party\Domain\Repository\PartyRepository;
use SJS\Neos\MCP\Domain\Model\ConnectionData;
use SJS\Neos\MCP\Domain\Repository\ConnectionDataRepository;


class ConnectionModuleController extends ActionController
{

    protected $defaultViewObjectName = FusionView::class;

    #[Flow\Inject()]
    protected Context $securityContext;

    #[Flow\Inject()]
    protected PartyRepository $partyRepository;

    #[Flow\Inject()]
    protected ConnectionDataRepository $connectionDataRepository;

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
        $connectionsByParty = [];
        foreach ($this->getAuthenticatedParties() as $party) {
            $connectionsByParty[] = [
                "party" => $party,
                "connections" => $this->connectionDataRepository->findByParty($party),
            ];
        }

        $this->view->assign("connectionsByParty", $connectionsByParty);
    }

    public function editAction(ConnectionData $connection): void
    {
        // TODO: sanity check if connection can be edited by current user

        $this->view->assign('connection', $connection);
        $this->view->assign('accounts', $this->getAccounts());
        $this->view->assign('roles', $this->policyService->getRoles());
        $this->view->assign('allowedRoles', array_fill_keys($connection->getOnlyAllowedRoleIdentifiers(), true));
    }

    public function newAction(): void
    {
        $this->view->assign('accounts', $this->getAccounts());
        $this->view->assign('roles', $this->policyService->getRoles());
    }

    /**
     * @param string $name
     * @param ?string $accountIdentifier
     * @param array<string> $onlyAllowedRoleIdentifiers
     * @return never
     */
    public function createAction(
        string $name,
        ?string $accountIdentifier = null,
        array $onlyAllowedRoleIdentifiers = []
    ): void {
        // TODO: do not just use the first one but let the user decide in the newAction for what party it should be added
        $party = $this->getAuthenticatedParties()[0];

        $connection = new ConnectionData();
        $connection->setParty($party);
        $connection->setName($name);
        $connection->setToken(bin2hex(random_bytes(32)));

        if ($accountIdentifier !== null) {
            foreach ($this->getAuthenticatedAccounts() as $account) {
                if ($account->getAccountIdentifier() === $accountIdentifier) {
                    $connection->setAccount($account);
                    break;
                }
            }
        }

        $connection->setOnlyAllowedRoleIdentifiers($onlyAllowedRoleIdentifiers);
        $this->connectionDataRepository->add($connection);

        // TODO: flash message after successful creation
        $this->redirect('index');
    }

    /**
     * @param ConnectionData $connection
     * @param string $name
     * @param ?string $account
     * @param array<string> $onlyAllowedRoleIdentifiers
     * @return never
     */
    public function updateAction(
        ConnectionData $connection,
        string $name,
        ?string $account = null,
        array $onlyAllowedRoleIdentifiers = []
    ): void {
        $connection->setName($name);

        $selectedAccount = null;
        if ($account !== null) {
            foreach ($this->getAuthenticatedAccounts() as $candidate) {
                if ($candidate->getAccountIdentifier() === $account) {
                    $selectedAccount = $candidate;
                    break;
                }
            }
        }
        $connection->setAccount($selectedAccount);
        $connection->setOnlyAllowedRoleIdentifiers($onlyAllowedRoleIdentifiers);

        $this->connectionDataRepository->update($connection);

        // TODO: flash message after successful update
        $this->redirect('index');
    }

    public function deleteAction(ConnectionData $connection): void
    {
        $this->connectionDataRepository->remove($connection);

        // TODO: flash message after successful deletion
        $this->redirect('index');
    }

    /**
     * @return array<Account>
     */
    protected function getAccounts(): array
    {
        $accounts = [];

        $parties = $this->getAuthenticatedParties();
        foreach ($parties as $party) {
            $accounts = [...$accounts, ...$party->getAccounts()];
        }

        return $accounts;
    }

    /**
     * @return array<Account>
     */
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
