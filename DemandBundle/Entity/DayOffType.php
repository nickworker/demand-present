<?php

namespace DemandBundle\Entity;

use AppBundle\Entity\Group;
use AppBundle\Security\Authorization\Model\Attribute;
use AppBundle\Security\Authorization\Model\OwnerInterface;
use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="day_off_type")
 * @ORM\Entity(repositoryClass="DemandBundle\Repository\DayOffTypeRepository")
 */
class DayOffType implements OwnerInterface, Attribute
{
    const PERIOD_MONTH  = 'month';
    const PERIOD_YEAR   = 'year';

    const DAYS_MONTH    = 30;
    const DAYS_YEAR     = 365;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=150)
     */
    private $title;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $owner;

    /**
     * @var bool
     * @ORM\Column(name="is_disabled", type="boolean", options={"default" : 0})
     */
    private $isDisabled = false;

    /**
     * @var string
     * @ORM\Column(name="period", type="string", length=70, nullable=true)
     */
    private $period;

    /**
     * @var bool - checks is automation mode enabled
     * @ORM\Column(name="is_auto", type="boolean", options={"default" : 0})
     */
    private $isAuto = false;

    /**
     * @var int - checks is automation mode enabled
     * @ORM\Column(name="days_amount", type="integer", nullable=true)
     */
    private $daysAmount;

    /**
     * @var \DateTime $createdAt
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="date")
     */
    private $createdAt;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     * @return $this
     */
    public function setOwner(User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return string
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @param string $period
     * @return $this
     */
    public function setPeriod($period)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsDisabled()
    {
        return $this->isDisabled;
    }

    /**
     * @param boolean $isDisabled
     * @return $this
     */
    public function setIsDisabled($isDisabled)
    {
        $this->isDisabled = $isDisabled;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsAuto()
    {
        return $this->isAuto;
    }

    /**
     * @param boolean $isAuto
     * @return $this
     */
    public function setIsAuto($isAuto)
    {
        $this->isAuto = $isAuto;

        return $this;
    }

    /**
     * @return null|\DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getDaysAmount()
    {
        return $this->daysAmount;
    }

    /**
     * @param int $daysAmount
     * @return $this
     */
    public function setDaysAmount($daysAmount)
    {
        $this->daysAmount = $daysAmount;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes()
    {
        return [
            Attribute::CREATE => [
                Group::GROUP_ADMIN,
                Group::GROUP_OWNER
            ],
            Attribute::VIEW_COMPANY => [
                Group::GROUP_ADMIN,
                Group::GROUP_DRH,
                Group::GROUP_MANAGER,
                Group::GROUP_OWNER
            ],
            Attribute::EDIT_COMPANY => [
                Group::GROUP_ADMIN,
                Group::GROUP_OWNER
            ],
            Attribute::DELETE_COMPANY => [
                Group::GROUP_ADMIN,
                Group::GROUP_OWNER
            ],
        ];
    }
}
