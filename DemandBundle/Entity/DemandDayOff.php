<?php

namespace DemandBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Model\DateStartEndInterface;
use AppBundle\Entity\Group;
use AppBundle\Security\Authorization\Model\Attribute;
use AppBundle\Security\Authorization\Model\OwnerInterface;

/**
 * @ORM\Entity(repositoryClass="DemandBundle\Repository\DemandDayOffRepository")
 */
class DemandDayOff extends Demand implements DateStartEndInterface, Attribute, OwnerInterface
{
    /**
     * @var \DateTime $startedAt
     *
     * @ORM\Column(name="started_at", type="date")
     */
    protected $startedAt;

    /**
     * @var \DateTime $endedAt
     *
     * @ORM\Column(name="ended_at", type="date")
     */
    protected $endedAt;

    /**
     * @var DayOffType
     * @ORM\ManyToOne(targetEntity="DemandBundle\Entity\DayOffType")
     * @ORM\JoinColumn(name="id_type", referencedColumnName="id")
     */
    protected $type;

    /**
     * @return \DateTime
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * @param \DateTime $startedAt
     * @return DemandDayOff
     */
    public function setStartedAt($startedAt)
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndedAt()
    {
        return $this->endedAt;
    }

    /**
     * @param \DateTime $endedAt
     * @return DemandDayOff
     */
    public function setEndedAt($endedAt)
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    /**
     * @return DayOffType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param DayOffType|null $type
     * @return $this
     */
    public function setType(DayOffType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return \AppBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->getUser();
    }


    /**
     * @return array
     */
    public function getAttributes()
    {
        return [
            Attribute::CREATE       => [
                Group::GROUP_EMPLOYEE,
                Group::GROUP_MANAGER,
                Group::GROUP_DRH
            ],
            Attribute::EDIT_OWNER   => [
                Group::GROUP_EMPLOYEE,
                Group::GROUP_MANAGER,
                Group::GROUP_DRH
            ],
            Attribute::EDIT_CHILD   => [
                Group::GROUP_OWNER,
                Group::GROUP_MANAGER,
                Group::GROUP_DRH
            ],
            Attribute::VIEW         => [
                Group::GROUP_SUPER_ADMIN,
                Group::GROUP_OWNER,
                Group::GROUP_DRH,
                Group::GROUP_ADMIN,
                Group::GROUP_EMPLOYEE,
                Group::GROUP_MANAGER
            ],
            Attribute::VIEW_CHILD   => [
                Group::GROUP_OWNER,
                Group::GROUP_DRH,
                Group::GROUP_MANAGER,
                Group::GROUP_ADMIN,
                Group::GROUP_SUPER_ADMIN
            ],
            Attribute::VIEW_OWNER   => [
                Group::GROUP_EMPLOYEE,
                Group::GROUP_MANAGER,
                Group::GROUP_DRH,
            ]
        ];
    }
}
