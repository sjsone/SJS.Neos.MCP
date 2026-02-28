<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\Controller\Backend;

use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Security\Context;
use Neos\Fusion\View\FusionView;
use Neos\Neos\Domain\Repository\UserRepository;
use Neos\Party\Domain\Model\AbstractParty;
use Neos\Flow\Annotations as Flow;
use Neos\Party\Domain\Repository\PartyRepository;
use SJS\Neos\MCP\Domain\Model\Agent;
use SJS\Neos\MCP\Domain\Repository\AgentRepository;


class AgentModuleController extends ActionController
{

    protected $defaultViewObjectName = FusionView::class;

    #[Flow\Inject()]
    protected Context $securityContext;

    #[Flow\Inject()]
    protected PartyRepository $partyRepository;

    #[Flow\Inject()]
    protected AgentRepository $agentRepository;

    #[Flow\Inject()]
    protected UserRepository $userRepository;

    public function indexAction(): void
    {
        $agentsByParty = [];
        foreach ($this->getAuthenticatedParties() as $party) {
            $agentsByParty[] = [
                "party" => $party,
                "agents" => $this->agentRepository->findByParty($party),
            ];
        }

        $this->view->assign("agentsByParty", $agentsByParty);
    }

    public function editAction(Agent $agent): void
    {
        // TODO: sanity check if agent can be edited by current user

        $this->view->assign("agent", $agent);
    }

    public function newAction(): void
    {

    }

    public function createAction(string $name): void
    {
        // TODO: do not just use the first one but let the user decide in the newAction for what party it should be added
        $party = $this->getAuthenticatedParties()[0];

        $agent = new Agent();
        $agent->setParty($party);
        $agent->setName($name);
        $agent->setToken(bin2hex(random_bytes(32)));

        $this->agentRepository->add($agent);

        // TODO: flash message after successful creation
        $this->redirect('index');
    }

    public function updateAction(Agent $agent, string $name): void
    {
        $agent->setName($name);

        $this->agentRepository->update($agent);

        // TODO: flash message after successful update
        $this->redirect('index');
    }

    public function deleteAction(Agent $agent): void
    {
        $this->agentRepository->remove($agent);

        // TODO: flash message after successful deletion
        $this->redirect('index');
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