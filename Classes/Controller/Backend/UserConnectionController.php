<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Controller\Backend;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Fusion\View\FusionView;
use Neos\Neos\Domain\Service\UserService;
use Neos\Party\Domain\Repository\PartyRepository;
use SJS\Neos\MCP\Domain\Model\ConnectionData;
use SJS\Neos\MCP\Domain\Repository\ConnectionDataRepository;

class UserConnectionController extends ActionController
{
    protected $defaultViewObjectName = FusionView::class;

    #[Flow\Inject]
    protected ConnectionDataRepository $connectionDataRepository;

    #[Flow\Inject]
    protected UserService $userService;

    #[Flow\Inject]
    protected PartyRepository $partyRepository;

    public function initializeView(\Neos\Flow\Mvc\View\ViewInterface $view): void
    {
        if ($view instanceof FusionView) {
            $view->setFusionPathPattern('resource://SJS.Neos.MCP/Private/Fusion/Module/UserConnection/Root.fusion');
        }
    }

    public function indexAction(): void
    {
        $currentUser = $this->userService->getCurrentUser();
        $currentAccountIdentifiers = [];
        foreach ($currentUser->getAccounts() as $account) {
            $currentAccountIdentifiers[] = $account->getAccountIdentifier();
        }

        $connections = [];
        foreach ($this->connectionDataRepository->findAll() as $connectionData) {
            $connectionAccount = $connectionData->getAccount();
            if ($connectionAccount === null || !in_array($connectionAccount->getAccountIdentifier(), $currentAccountIdentifiers, true)) {
                continue;
            }
            $connections[] = $connectionData;
        }
        $this->view->assign('connections', $connections);
    }

    public function newAction(): void
    {
    }

    public function createAction(): void
    {
        $connection = new ConnectionData();
        $connection->setName($this->request->getArgument('name'));
        $connection->setToken(\bin2hex(\random_bytes(32)));

        $currentUser = $this->userService->getCurrentUser();
        $accounts = $currentUser->getAccounts();
        if (count($accounts) > 0) {
            $account = $accounts[0];
            $connection->setAccount($account);
            $party = $this->partyRepository->findOneHavingAccount($account);
            if ($party !== null) {
                $connection->setParty($party);
            }
        }

        $this->connectionDataRepository->add($connection);
        $this->redirect('index');
    }

    public function editAction(ConnectionData $connection): void
    {
        $this->view->assign('connection', $connection);
    }

    public function updateAction(ConnectionData $connection): void
    {
        $connection->setName($this->request->getArgument('name'));
        $this->connectionDataRepository->update($connection);
        $this->redirect('index');
    }

    public function deleteAction(ConnectionData $connection): void
    {
        $this->connectionDataRepository->remove($connection);
        $this->redirect('index');
    }
}
