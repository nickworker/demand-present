<?php

namespace DemandBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Group;
use AppBundle\Security\Authorization\Model\Attribute;
use AppBundle\Security\Authorization\Model\OwnerInterface;

/**
 * @ORM\Table(name="day_off_rest")
 * @ORM\Entity(repositoryClass="DemandBundle\Repository\DayOffRestRepository")
 */
class DayOffRest implements Attribute, OwnerInterface
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INIT     = 'init';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="restDaysOffs")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @var DayOffType
     * @ORM\ManyToOne(targetEntity="DemandBundle\Entity\DayOffType")
     * @ORM\JoinColumn(name="id_type", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $type;

    /**
     * @var int
     * @ORM\Column(name="amount", type="integer")
     */
    protected $amount;

    /**
     * @var int - real used amount based on demand day off updates
     * @ORM\Column(name="real_amount", type="integer", options={"default" : 0})
     */
    protected $realAmount = 0;

    /**
     * @var int
     * @ORM\Column(name="status", type="string", length=100)
     */
    protected $status;

    /**
     * @var \DateTime $autoDate
     *
     * @ORM\Column(name="auto_date", type="date", nullable=true)
     */
    protected $autoDate;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

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
     * @param DayOffType $type
     * @return $this
     */
    public function setType(DayOffType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->getUser();
    }

    /**
     * @return \DateTime
     */
    public function getAutoDate()
    {
        return $this->autoDate;
    }

    /**
     * @param \DateTime $autoDate
     * @return $this
     */
    public function setAutoDate($autoDate)
    {
        $this->autoDate = $autoDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getRealAmount()
    {
        return $this->realAmount;
    }

    /**
     * @param $realAmount
     * @return $this
     */
    public function setRealAmount($realAmount)
    {
        $this->realAmount = $realAmount;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return [
            Attribute::CREATE => [
                Group::GROUP_MANAGER,
                Group::GROUP_DRH,
            ],
            Attribute::EDIT_CHILD  => [
                Group::GROUP_OWNER,
                Group::GROUP_MANAGER,
                Group::GROUP_DRH
            ],
            Attribute::VIEW_CHILD  => [
                Group::GROUP_OWNER,
                Group::GROUP_MANAGER,
                Group::GROUP_DRH,
                Group::GROUP_ADMIN,
                Group::GROUP_SUPER_ADMIN
            ],
            Attribute::VIEW_OWNER  => [
                Group::GROUP_MANAGER,
                Group::GROUP_DRH,
                Group::GROUP_EMPLOYEE,
                Group::GROUP_ADMIN,
                Group::GROUP_SUPER_ADMIN
            ]
        ];
    }
}
