<?php


namespace DemandBundle\EventListener\Entity;

use DemandBundle\Entity\DayOffType;
use DemandBundle\Manager\DayOffRestManager;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DayOffTypeListener
{
    /**
     * @var DayOffRestManager
     */
    private $dayOffRestManager;

    /**
     * @param DayOffRestManager $dayOffRestManager
     */
    public function __construct(DayOffRestManager $dayOffRestManager)
    {
        $this->dayOffRestManager = $dayOffRestManager;
    }

    /**
     * @param DayOffType $dayOffType
     * @param LifecycleEventArgs $args
     */
    public function postPersist(DayOffType $dayOffType, LifecycleEventArgs $args)
    {
        if ($dayOffType->getIsAuto()) {
            $this->dayOffRestManager->createFromDayOffType($dayOffType);
        }
    }

    /**
     * @param DayOffType $dayOffType
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(DayOffType $dayOffType, LifecycleEventArgs $args)
    {
        if ($dayOffType->getIsAuto()) {
            $this->dayOffRestManager->updateFromDayOffType($dayOffType);
        }
    }
}
