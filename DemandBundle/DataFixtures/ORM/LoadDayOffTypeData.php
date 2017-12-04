<?php

namespace DemandBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use DemandBundle\Entity\DayOffType;
use AppBundle\Entity\User;
use AppBundle\Entity\Group;

class LoadDayOffTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $data = require 'Data/dayOffTypeData.php';

        foreach ($manager->getRepository(User::class)->findByGroupSystemName(Group::GROUP_OWNER) as $user) {
            foreach ($data as $item) {
                $entity = new DayOffType();

                $entity->setOwner($user);

                foreach ($item as $key => $value) {
                    $entity->{'set'.ucfirst($key)}($value);
                }

                $manager->persist($entity);
            }
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 800;
    }
}
