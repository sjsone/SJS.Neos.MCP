<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Controller\Backend;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use SJS\Neos\MCP\Domain\Model\IdentityData;
use SJS\Neos\MCP\Domain\Repository\IdentityDataRepository;

class IdentityModuleController extends ActionController
{
    #[Flow\Inject]
    protected IdentityDataRepository $identityDataRepository;

    public function indexAction(): void
    {
        $identitiesByParty = [];
        foreach ($this->identityDataRepository->findAll() as $identityData) {
            $party = $identityData->getParty();
            $partyKey = (string)$party;
            if (!isset($identitiesByParty[$partyKey])) {
                $identitiesByParty[$partyKey] = [
                    'party' => $party,
                    'identities' => [],
                ];
            }
            $identitiesByParty[$partyKey]['identities'][] = $identityData;
        }
        $this->view->assign('identitiesByParty', $identitiesByParty);
    }

    public function newAction(): void
    {
    }

    public function createAction(): void
    {
        $identity = new IdentityData();
        $identity->setName($this->request->getArgument('name'));
        $identity->setToken(\bin2hex(\random_bytes(32)));
        $this->identityDataRepository->add($identity);
        $this->redirect('index');
    }

    public function editAction(IdentityData $identity): void
    {
        $this->view->assign('identity', $identity);
    }

    public function updateAction(IdentityData $identity): void
    {
        $identity->setName($this->request->getArgument('name'));
        $this->identityDataRepository->update($identity);
        $this->redirect('index');
    }

    public function deleteAction(IdentityData $identity): void
    {
        $this->identityDataRepository->remove($identity);
        $this->redirect('index');
    }
}
