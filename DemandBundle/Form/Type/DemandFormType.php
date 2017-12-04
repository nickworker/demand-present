<?php

namespace DemandBundle\Form\Type;

use AppBundle\Manager\UserManager;
use DemandBundle\Entity\Demand;
use DemandBundle\Manager\DemandManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

abstract class DemandFormType extends AbstractType
{
    const FIELD_TRANSITION = 'transition';

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return \AppBundle\Entity\User|null
     */
    protected function getUser()
    {
        $user = $this->userManager->getUser();

        if (!$user) {
            throw new AuthenticationException();
        }

        return $user;
    }

    /**
     * @param Demand|null $demand
     * @return array
     */
    protected function getTransitionChoices(Demand $demand = null)
    {
        if ($demand) {
            $states  = DemandManager::getStateByUserRelation($demand, $this->getUser());

            return count($states) ? array_combine($states, $states) : [];
        }

        $stateByUserGroups  = DemandManager::getStateByUserGroups();
        $group              = $this->getUser()->getGroupSystemName();

        return isset($stateByUserGroups[$group])
            ? array_combine($stateByUserGroups[$group], $stateByUserGroups[$group]) : [];
    }

    abstract protected function preSetData(FormEvent $event);
}
