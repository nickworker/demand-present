<?php

namespace DemandBundle\EventListener\Schedule;

use Doctrine\ORM\EntityManagerInterface;
use ScheduleBundle\Entity\Event;
use ScheduleBundle\Entity\EventType;
use ScheduleBundle\Event\UploadEvent;
use ScheduleBundle\ScheduleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use DemandBundle\Entity\DemandDayOff;

class DemandDayOffListener implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [ScheduleEvents::SCHEDULE_EVENT_UPLOAD => 'onUpload'];
    }

    /**
     * @param UploadEvent $event
     */
    public function onUpload(UploadEvent $event)
    {
        /** @var \DemandBundle\Entity\DemandDayOff $subject */
        $subject    = $event->getSubject();


        if (!($subject instanceof DemandDayOff) || ($subject->getState() != DemandDayOff::STATE_ACCEPT_BY_DRH)) {
            return;
        }

        $item       = new Event();
        $eventType  = $this->entityManager->getRepository(EventType::class)->findOneBy(
            ['systemName' => EventType::TYPE_LEAVE]
        );

        $item
            ->setStartedAt($subject->getStartedAt())
            ->setEndedAt($subject->getEndedAt())
            ->setType($eventType)
            ->setUser($subject->getOwner());

        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }
}
