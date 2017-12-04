<?php

namespace DemandBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use DemandBundle\Entity\DemandPayment;
use AppBundle\Entity\User;
use AppBundle\Entity\Group;

class LoadDemandPayment extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $users      = $manager->getRepository(User::class)->findByExcludedGroups([
            Group::GROUP_SUPER_ADMIN, Group::GROUP_ADMIN, Group::GROUP_OWNER
        ]);

        /** @var \AppBundle\Entity\User $user */
        foreach ($users as $key => $user) {
            $year   = isset($key) ? 2000 + $key + 1 : 2000;
            $month  = (isset($month) && $month < 12) ? $month + 1 : 1;
            $entity = new DemandPayment();
            $entity
                 ->setUser($user)
                 ->setYear($year)
                 ->setMonth($month)
                 ->setAmount($key);

            if ($this->container->get('state_machine.demand')->can($entity, DemandPayment::STATE_START)) {
                $this->container->get('state_machine.demand')->apply($entity, DemandPayment::STATE_START);
            }

            $manager->persist($entity);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 810;
    }
}
