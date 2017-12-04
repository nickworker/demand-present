<?php

namespace DemandBundle\EventListener\Workflow;

use AppBundle\Manager\UserManager;
use DemandBundle\Entity\DemandDayOff;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use DemandBundle\Manager\DemandManager;
use DemandBundle\Entity\Demand;

class DemandWorkflowListener implements EventSubscriberInterface
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param GuardEvent $event
     */
    public function guardUpdate(GuardEvent $event)
    {
        $entity = $event->getSubject();

        if ($entity instanceof Demand) {
            $user = $this->userManager->getUser();

            //not auth user hook for fixtures
            if ($user) {
                if (!in_array($event->getTransition()->getName(), $this->getAllowedStates($event->getSubject()))) {
                    $event->setBlocked(true);
                }
            }
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.demand.guard' => ['guardUpdate'],
        ];
    }

    /**
     * @param Demand $demand
     * @return array
     */
    private function getAllowedStates(Demand $demand)
    {
        if ($demand instanceof DemandDayOff) {
            $states  = DemandManager::getStateByUserRelation($demand, $this->userManager->getUser());

            return $states;
        }

        $statesByUserGroups = DemandManager::getStateByUserGroups();
        $group              = $this->userManager->getUser()->getGroupSystemName();

        return isset($statesByUserGroups[$group]) ? $statesByUserGroups[$group] : [];
    }
}
