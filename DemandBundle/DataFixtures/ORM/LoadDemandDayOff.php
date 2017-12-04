<?php

namespace DemandBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use DemandBundle\Entity\DayOffType;
use DemandBundle\Entity\DemandDayOff;
use AppBundle\Entity\User;
use AppBundle\Entity\Group;

class LoadDemandDayOff extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $typesByCompany  = $this->getDayOffTypesByCompany($manager);
        $users      = $manager->getRepository(User::class)->findByExcludedGroups([
            Group::GROUP_SUPER_ADMIN, Group::GROUP_ADMIN, Group::GROUP_OWNER
        ]);

        $i = 1;

        /** @var \AppBundle\Entity\User $user */
        foreach ($users as $key => $user) {
            $types = isset($typesByCompany[$user->getCompany()->getId()])
                ? $typesByCompany[$user->getCompany()->getId()] : [];

            $entity = new DemandDayOff();
            $entity
                ->setUser($user)
                ->setType(next($types) ? current($types) : reset($types))
                ->setStartedAt(new \DateTime(sprintf('-%d days', $i + 3)))
                ->setEndedAt(new \DateTime(sprintf('-%d days', $i)))
            ;

            if ($this->container->get('state_machine.demand')->can($entity, DemandDayOff::STATE_START)) {
                $this->container->get('state_machine.demand')->apply($entity, DemandDayOff::STATE_START);
            }

            $i = $i < 10 ? $i + 1 : 1;

            $manager->persist($entity);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 820;
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
