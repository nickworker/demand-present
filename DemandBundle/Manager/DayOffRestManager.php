<?php

namespace DemandBundle\Manager;

use AppBundle\Entity\Group;
use AppBundle\Manager\UserManager;
use DemandBundle\Request\DayOffRestCollectionRequest;
use AppBundle\Entity\User;
use DemandBundle\Entity\DayOffRest;
use DemandBundle\Entity\DayOffType;
use Doctrine\ORM\EntityManagerInterface;
use DemandBundle\Entity\DemandDayOff;
use SurveyBundle\Manager\SurveyManager;

class DayOffRestManager
{
    const FILTER                = 'filter';
    const FILTER_TYPE           = 'type';
    const FILTER_USER           = 'user';
    const FILTER_IS_AUTO_DATE   = 'is_auto_date';
    const FILTER_STATUS         = 'status';
    const FILTER_AUTO_DATE      = 'auto_date';
    const FILTER_YES            = 'yes';
    const FILTER_NO             = 'no';


    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserManager $userManager
     */
    public function __construct(EntityManagerInterface $entityManager, UserManager $userManager)
    {
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
    }

    /**
     * @param User $user
     * @return DayOffRestCollectionRequest
     */
    public function buildRequest(User $user)
    {
        $request        = new DayOffRestCollectionRequest();
        $days           = $this->getListForModification($user, [DayOffRest::STATUS_INIT]);
        $request->days  = $days[DayOffRest::STATUS_INIT];

        return $request;
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function getListForModification(User $user, array $statuses)
    {
        $result = [DayOffRest::STATUS_INIT => [], DayOffRest::STATUS_ACTIVE => []];
        $days   = $this->entityManager->getRepository(DayOffRest::class)->findByAutoAndDisabledMode(
            $user->getId(),
            $statuses
        );

        /** @var \DemandBundle\Entity\DayOffRest $day */
        foreach ($days as $day) {
            $result[$day->getStatus()][] = $day;
        }

        return $result;
    }

    /**
     * @param User $user
     * @param DayOffRestCollectionRequest $request
     * @return array
     */
    public function updateFromRequest(User $user, DayOffRestCollectionRequest $request)
    {
        $daysByStatus   = $this->getListForModification($user, [DayOffRest::STATUS_INIT, DayOffRest::STATUS_ACTIVE]);
        $activeDays     = [DayOffRest::STATUS_INIT => [], DayOffRest::STATUS_ACTIVE => []];
        $result         = [];

        foreach ($daysByStatus as $status => $days) {
            foreach ($days as $day) {
                $activeDays[$status][$day->getType()->getId()] = $day;
            }
        }

        /** @var \DemandBundle\Entity\DayOffRest $day */
        foreach ($request->days as $day) {
            $amount = $day->getAmount();
            $typeId = $day->getType()->getId();

            $day    = isset($activeDays[DayOffRest::STATUS_INIT][$typeId])
                    ? $activeDays[DayOffRest::STATUS_INIT][$typeId] : $day;

            $day->setAmount($amount);

            $dayToRebuild = (isset($activeDays[DayOffRest::STATUS_ACTIVE][$typeId]))
                    ? $activeDays[DayOffRest::STATUS_ACTIVE][$typeId] : new DayOffRest();

            $this->reBuildDayOffRest($dayToRebuild, $day);

            $this->entityManager->persist($day);
            $this->entityManager->persist($dayToRebuild);

            array_push($result, $day);
            array_push($result, $dayToRebuild);
        }

        $this->entityManager->flush();

        return $result;
    }

    /**
     * @param DemandDayOff $dayOff
     */
    public function updateFromDemand(DemandDayOff $dayOff)
    {
        if ($dayOff->getState() == DemandDayOff::STATE_ACCEPT_BY_DRH) {
            /** @var \DemandBundle\Entity\DayOffRest $restDaysOff */
            $restDaysOff = $this->entityManager->getRepository(DayOffRest::class)->findOneBy([
                'user' => $dayOff->getUser(), 'type' => $dayOff->getType(), 'status' => DayOffRest::STATUS_ACTIVE
            ]);

            if ($restDaysOff) {
                $datesDiff  = $dayOff->getEndedAt()->diff($dayOff->getStartedAt());
                $daysDiff   = $restDaysOff->getAmount() - ($datesDiff->days + 1);

                $restDaysOff
                    ->setAmount($daysDiff <= 0 ? 0 : $daysDiff)
                    ->setRealAmount($restDaysOff->getRealAmount() + $datesDiff->days + 1);
            }
        }
    }

    /**
     * @param DayOffType $dayOffType
     * @param bool|true $withFlush
     */
    public function createFromDayOffType(DayOffType $dayOffType, $withFlush = true)
    {
        $users      = $this->userManager->getUserChildren($dayOffType->getOwner());
        $statuses   = [DayOffRest::STATUS_ACTIVE, DayOffRest::STATUS_INIT];
        /** @var \AppBundle\Entity\User $user */
        foreach ($users as $user) {
            if ($user->hasGroup(Group::GROUP_ADMIN)) {
                continue;
            }

            foreach ($statuses as $status) {
                $entity     = new DayOffRest();
                $autoDate   = $status == DayOffRest::STATUS_ACTIVE ? $dayOffType->getCreatedAt() : null;

                $entity
                    ->setUser($user)
                    ->setAutoDate($autoDate)
                    ->setAmount($dayOffType->getDaysAmount())
                    ->setStatus($status)
                    ->setType($dayOffType)
                    ->setRealAmount(0);

                $this->entityManager->persist($entity);
            }
        }

        if ($withFlush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param DayOffType $dayOffType
     * @param bool|true $withFlush
     */
    public function updateFromDayOffType(DayOffType $dayOffType, $withFlush = true)
    {
        $ids        = $this->userManager->getChildrenIds($dayOffType->getOwner(), [], false);
        $daysQuery  = $this->entityManager->getRepository(DayOffRest::class)->findForListQuery([
            static::FILTER => [
                static::FILTER_USER         => $ids,
                static::FILTER_STATUS       => DayOffRest::STATUS_ACTIVE
            ]
        ]);

        /** @var \DemandBundle\Entity\DayOffRest $day */
        foreach ($daysQuery->getResult() as $day) {
            $daysAmount = $this->getDaysAmount($day->getAmount(), $dayOffType->getDaysAmount(), $day->getRealAmount());
            $day
                ->setAutoDate($day->getAutoDate() ? $day->getAutoDate() : $dayOffType->getCreatedAt())
                ->setAmount($daysAmount);
            $this->entityManager->persist($day);
        }

        if ($withFlush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param User $user
     * @param bool|true $withFlush
     */
    public function initForUser(User $user, $withFlush = true)
    {
        $statuses   = [DayOffRest::STATUS_ACTIVE, DayOffRest::STATUS_INIT];
        $types      = $this->entityManager->getRepository(DayOffType::class)->findBy([
            'owner'     => $user->getCompany()->getUser(),
            'isAuto'    => true
        ]);
        /** @var \DemandBundle\Entity\DayOffType $type */
        foreach ($types as $type) {
            $timeOffset = $type->getPeriod() == DayOffType::PERIOD_MONTH
                ? DayOffType::DAYS_MONTH * SurveyManager::DAY : DayOffType::DAYS_YEAR * SurveyManager::DAY;
            foreach ($statuses as $status) {
                $entity     = new DayOffRest();
                $autoDate   = $status == DayOffRest::STATUS_ACTIVE
                    ? \DateTime::createFromFormat('U', $type->getCreatedAt()->getTimestamp() + $timeOffset) : null;

                $entity
                    ->setUser($user)
                    ->setAutoDate($autoDate)
                    ->setAmount($type->getDaysAmount())
                    ->setStatus($status)
                    ->setType($type);

                $this->entityManager->persist($entity);
            }
        }

        if ($withFlush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param \DateTime $date
     */
    public function autoUpdate(\DateTime $date)
    {
        $days = $this->entityManager->getRepository(DayOffRest::class)->findForAutoUpdate($date);

        /** @var \DemandBundle\Entity\DayOffRest $day */
        foreach ($days as $day) {
            $type       = $day->getType();
            $timeOffset = $type->getPeriod() == DayOffType::PERIOD_MONTH
                        ? DayOffType::DAYS_MONTH * SurveyManager::DAY
                        : DayOffType::DAYS_YEAR * SurveyManager::DAY;
            $daysAmount = $type->getPeriod() == DayOffType::PERIOD_MONTH
                ? $day->getAmount() + $type->getDaysAmount()
                : $type->getDaysAmount();

            $day->setAmount($type->getIsDisabled() ? $day->getAmount() : $daysAmount)
                ->setAutoDate(\DateTime::createFromFormat('U', $date->getTimestamp() + $timeOffset))
                ->setRealAmount(0);
        }

        $this->entityManager->flush();
    }

    /**
     * @param DayOffRest $dayOffRest
     * @param DayOffRest $data
     * @return DayOffRest
     */
    private function reBuildDayOffRest(DayOffRest $dayOffRest, DayOffRest $data)
    {
        $amount = $this->getDaysAmount($dayOffRest->getAmount(), $data->getAmount(), $dayOffRest->getRealAmount());

        $dayOffRest
            ->setStatus(DayOffRest::STATUS_ACTIVE)
            ->setAmount($amount)
            ->setType($data->getType())
            ->setUser($data->getUser())
            ->setRealAmount(0);

        return $dayOffRest;
    }

    /**
     * @param $oldAmount
     * @param $newAmount
     * @param $realAmount
     * @return int
     */
    private function getDaysAmount($oldAmount, $newAmount, $realAmount)
    {
        if ($newAmount < $oldAmount) {
            return $newAmount;
        } else {
            return ($newAmount - $realAmount < 0) ? 0 : $newAmount - $realAmount;
        }
    }
}
