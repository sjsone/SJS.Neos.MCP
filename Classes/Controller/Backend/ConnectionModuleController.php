<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Controller\Backend;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Security\AccountRepository;
use Neos\Fusion\View\FusionView;
use Neos\Party\Domain\Repository\PartyRepository;
use SJS\Neos\MCP\Domain\Model\ConnectionData;
use SJS\Neos\MCP\Domain\Repository\ConnectionDataRepository;

class ConnectionModuleController extends ActionController
{
    protected $defaultViewObjectName = FusionView::class;

    #[Flow\Inject]
    protected ConnectionDataRepository $connectionDataRepository;

    #[Flow\Inject]
    protected AccountRepository $accountRepository;

    #[Flow\Inject]
    protected PartyRepository $partyRepository;

    public function initializeView(\Neos\Flow\Mvc\View\ViewInterface $view): void
    {
        if ($view instanceof FusionView) {
            $view->setFusionPathPattern('resource://SJS.Neos.MCP/Private/Fusion/Module/Root.fusion');
        }
    }

    public function indexAction(): void
    {
        $connectionsByParty = [];
        foreach ($this->connectionDataRepository->findAll() as $connectionData) {
            $party = $connectionData->getParty();
            $partyKey = \spl_object_hash($party);
            if (!isset($connectionsByParty[$partyKey])) {
                $connectionsByParty[$partyKey] = [
                    'party' => $party,
                    'connections' => [],
                ];
            }
            $connectionsByParty[$partyKey]['connections'][] = $connectionData;
        }
        $this->view->assign('connectionsByParty', $connectionsByParty);
    }

    public function newAction(): void
    {
        $this->view->assign('accounts', $this->accountRepository->findAll()->toArray());
    }

    public function createAction(): void
    {
        $connection = new ConnectionData();
        $connection->setName($this->request->getArgument('name'));
        $connection->setToken(\bin2hex(\random_bytes(32)));

        $accountIdentifier = $this->request->getArgument('account');
        if (!empty($accountIdentifier)) {
            $account = null;
            foreach ($this->accountRepository->findAll() as $candidate) {
                if ($candidate->getAccountIdentifier() === $accountIdentifier) {
                    $account = $candidate;
                    break;
                }
            }
            if ($account !== null) {
                $connection->setAccount($account);
                $party = $this->partyRepository->findOneHavingAccount($account);
                if ($party !== null) {
                    $connection->setParty($party);
                }
            }
        }

        $this->connectionDataRepository->add($connection);
        $this->redirect('index');
    }

    public function editAction(ConnectionData $connection): void
    {
        $this->view->assign('connection', $connection);
        $this->view->assign('accounts', $this->accountRepository->findAll()->toArray());
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
