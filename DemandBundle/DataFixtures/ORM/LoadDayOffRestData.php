<?php

namespace DemandBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use DemandBundle\Entity\DayOffType;
use DemandBundle\Entity\DayOffRest;
use AppBundle\Entity\User;
use AppBundle\Entity\Group;

class LoadDayOffRestData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $typesByCompany  = $this->getDayOffTypesByCompany($manager);
        $users          = $manager->getRepository(User::class)->findByExcludedGroups([
            Group::GROUP_ADMIN,
            Group::GROUP_SUPER_ADMIN,
            Group::GROUP_OWNER
        ]);

        /** @var \AppBundle\Entity\User $user */
        foreach ($users as $key => $user) {
            $types = isset($typesByCompany[$user->getCompany()->getId()])
                ? $typesByCompany[$user->getCompany()->getId()] : [];

            foreach ($types as $type) {
                foreach ([DayOffRest::STATUS_ACTIVE, DayOffRest::STATUS_INIT] as $restStatus) {
                    $entity = new DayOffRest();

                    $entity
                        ->setUser($user)
                        ->setType($type)
                        ->setAmount(0)
                        ->setStatus($restStatus);

                    $manager->persist($entity);
                }
            }
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 830;
    }

    /**
     * @param ObjectManager $manager
     * @return array
     */
    private function getDayOffTypesByCompany(ObjectManager $manager)
    {
        $result = [];

        /** @var \DemandBundle\Entity\DayOffType $val */
        foreach ($manager->getRepository(DayOffType::class)->findAll() as $val) {
            $result[$val->getOwner()->getCompany()->getId()][] = $val;
        }

        return $result;
    }
}
