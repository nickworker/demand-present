<?php

namespace DemandBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @ORM\Table(name="demand")
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type_disc", type="string")
 * @ORM\DiscriminatorMap({"day_off" = "DemandDayOff", "payment" = "DemandPayment"})
 */
abstract class Demand
{
    const TYPE_DAY_OFF              = 'day_off';
    const TYPE_PAYMENT              = 'payment';

    const STATE_START               = 'start'; //employee
    const STATE_ACCEPT_BY_MANAGER   = 'accept_by_manager'; //manager
    const STATE_ACCEPT_BY_DRH       = 'accept_by_drh'; //DRH
    const STATE_REJECT              = 'reject'; //employee, manager, DRH
    const STATE_REOPEN              = 'reopen';//employee

    const INTERNAL_STATE_WAITING_FOR_MANAGER    = 'waiting_for_manager';
    const INTERNAL_STATE_WAITING_FOR_DRH        = 'waiting_for_drh';

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @var string
     * @ORM\Column(name="state", type="string", length=255)
     */
    protected $state;

    /**
     * @var string
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    protected $note;

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
     * @param User|null $user
     * @return $this
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return Demand
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param $note
     * @return $this
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }
}
