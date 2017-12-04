<?php

namespace DemandBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DayOffRestCommand extends ContainerAwareCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('user:day-off-rest:auto-prolong')
            ->setDescription('Updates User Day Off Rest');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \DemandBundle\Manager\DayOffRestManager $manager */
        $manager    = $this->getContainer()->get('demand.manager.day_off_rest');

        $manager->autoUpdate(new \DateTime());
    }
}
