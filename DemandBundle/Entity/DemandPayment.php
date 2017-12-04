<?php

namespace DemandBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Group;
use AppBundle\Security\Authorization\Model\Attribute;
use AppBundle\Security\Authorization\Model\OwnerInterface;

/**
 * @ORM\Entity(repositoryClass="DemandBundle\Repository\DemandPaymentRepository")
 */
class DemandPayment extends Demand implements Attribute, OwnerInterface
{
    /**
     * @var float
     * @ORM\Column(name="amount", type="decimal", scale=2)
     */
    protected $amount;

    /**
     * @var int
     * @ORM\Column(name="year", type="integer")
     */
    protected $year;

    /**
     * @var int
     * @ORM\Column(name="month", type="integer")
     */
    protected $month;

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return DemandPayment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param int $year
     * @return DemandPayment
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @param int $month
     * @return DemandPayment
     */
    public function setMonth($month)
    {
        $this->month = $month;

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
                Group::GROUP_EMPLOYEE
            ],
            Attribute::EDIT_OWNER   => [
                Group::GROUP_EMPLOYEE
            ],
            Attribute::EDIT_CHILD   => [
                Group::GROUP_OWNER,
                Group::GROUP_MANAGER,
                Group::GROUP_DRH
            ],
            Attribute::VIEW   => [
                Group::GROUP_SUPER_ADMIN,
                Group::GROUP_OWNER,
                Group::GROUP_DRH,
                Group::GROUP_ADMIN,
                Group::GROUP_EMPLOYEE
            ],
            Attribute::VIEW_CHILD   => [
                Group::GROUP_OWNER,
                Group::GROUP_MANAGER,
                Group::GROUP_DRH,
                Group::GROUP_ADMIN,
                Group::GROUP_SUPER_ADMIN
            ],
            Attribute::VIEW_OWNER   => [
                Group::GROUP_EMPLOYEE
            ]
        ];
    }
}
