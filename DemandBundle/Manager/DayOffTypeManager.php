<?php

namespace DemandBundle\Manager;

use AppBundle\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\User;
use DemandBundle\Entity\DayOffType;

class DayOffTypeManager
{
    const FILTER                = 'filter';
    const FILTER_YES            = 'yes';
    const FILTER_NO             = 'no';
    const FILTER_IS_DISABLED    = 'is_disabled';
    const FILTER_IS_AUTO        = 'is_auto';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user
     * @param array $params
     * @return mixed
     */
    public function getListQuery(User $user, array $params = [])
    {
        $params = $this->rebuildParams($params);

        if (!$user->hasGroup(Group::GROUP_OWNER) && !$user->hasGroup(Group::GROUP_ADMIN)) {
            if (!isset($params[static::FILTER])) {
                $params[static::FILTER] = [];
            }

            $params[static::FILTER][static::FILTER_IS_DISABLED] = static::FILTER_NO;
        }

        return $this->entityManager->getRepository(DayOffType::class)->findForCompanyListQuery(
            $user->getCompany()->getId(),
            $params
        );
    }

    /**
     * @param $data
     * @return array
     */
    private function rebuildParams($data)
    {
        foreach ($data as &$val) {
            if (is_array($val)) {
                $val = $this->rebuildParams($val);
            }
        }

        return array_filter($data);
    }
}