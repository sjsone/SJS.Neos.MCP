<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Controller\Backend;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Security\AccountRepository;
use Neos\Fusion\View\FusionView;
use Neos\Party\Domain\Model\AbstractParty;
use Neos\Party\Domain\Repository\PartyRepository;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use SJS\Neos\MCP\Domain\Model\ConnectionData;
use SJS\Neos\MCP\Domain\Repository\ConnectionDataRepository;

class AdministrationModuleController extends ActionController
{
    use FusionViewTrait;

    protected $defaultViewObjectName = FusionView::class;

    #[Flow\Inject]
    protected ConnectionDataRepository $connectionDataRepository;

    #[Flow\Inject]
    protected AccountRepository $accountRepository;

    #[Flow\Inject]
    protected PartyRepository $partyRepository;

    #[Flow\Inject]
    /**
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * List all parties that have MCP connections, with summary counts.
     */
    public function indexAction(): void
    {
        $partyMap = [];
        $seenPartyIds = [];

        foreach ($this->connectionDataRepository->findAll() as $connectionData) {
            $party = $connectionData->getParty();
            $partyId = $this->persistenceManager->getIdentifierByObject($party);

            if (!isset($partyMap[$partyId])) {
                $partyMap[$partyId] = [
                    'party' => $party,
                    'connectionCount' => 0,
                ];
            }
            $partyMap[$partyId]['connectionCount']++;
        }

        $this->view->assign('parties', \array_values($partyMap));
    }

    /**
     * Show a single party's MCP connections.
     */
    public function partyAction(AbstractParty $party): void
    {
        $connections = [];
        foreach ($this->connectionDataRepository->findAll() as $connectionData) {
            if ($connectionData->getParty() === $party) {
                $connections[] = $connectionData;
            }
        }

        $this->view->assign('party', $party);
        $this->view->assign('connections', $connections);
    }

    public function newAction(AbstractParty $party = null): void
    {
        // Only show accounts belonging to the selected party
        $accounts = [];
        if ($party !== null) {
            foreach ($party->getAccounts() as $account) {
                $accounts[] = $account;
            }
        }

        $this->view->assign('party', $party);
        $this->view->assign('accounts', $accounts);
    }

    public function createAction(): void
    {
        $connection = new ConnectionData();
        $connection->setName($this->request->getArgument('name'));
        $connection->setToken(\bin2hex(\random_bytes(32)));
        $connection->setSourceIdentifier('neos-backend');

        $party = $this->request->getArgument('party');
        if ($party instanceof AbstractParty) {
            $connection->setParty($party);
        }

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
                if (!$party instanceof AbstractParty) {
                    $party = $this->partyRepository->findOneHavingAccount($account);
                    if ($party !== null) {
                        $connection->setParty($party);
                    }
                }
            }
        }

        $this->connectionDataRepository->add($connection);

        $redirectParty = $connection->getParty();
        if ($redirectParty !== null) {
            $this->redirect('party', null, null, ['party' => $redirectParty]);
        } else {
            $this->redirect('index');
        }
    }

    public function editAction(ConnectionData $connection): void
    {
        // Only show accounts belonging to the connection's party
        $accounts = [];
        $party = $connection->getParty();
        foreach ($party->getAccounts() as $account) {
            $accounts[] = $account;
        }

        $this->view->assign('connection', $connection);
        $this->view->assign('accounts', $accounts);
    }

    public function updateAction(ConnectionData $connection): void
    {
        $connection->setName($this->request->getArgument('name'));
        $this->connectionDataRepository->update($connection);

        $party = $connection->getParty();
        $this->redirect('party', null, null, ['party' => $party]);
    }

    public function deleteAction(ConnectionData $connection): void
    {
        $party = $connection->getParty();
        $this->connectionDataRepository->remove($connection);

        $this->redirect('party', null, null, ['party' => $party]);
    }
}
