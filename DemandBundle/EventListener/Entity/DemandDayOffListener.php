<?php

namespace DemandBundle\EventListener\Entity;

use DemandBundle\Entity\DemandDayOff;
use DemandBundle\Manager\DayOffRestManager;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class DemandDayOffListener
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
     * @param DemandDayOff $dayOff
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(DemandDayOff $dayOff, PreUpdateEventArgs $args)
    {
        $this->dayOffRestManager->updateFromDemand($dayOff);
    }
}
