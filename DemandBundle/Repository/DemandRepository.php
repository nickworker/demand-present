<?php

namespace DemandBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Helper\DoctrineQueryHelper;

abstract class DemandRepository extends EntityRepository
{
    abstract public function getByUserQuery($userId);
    abstract public function getByUsersIdQuery(array $userIds);

    /**
     * @param $userId
     * @return array
     */
    public function findCountByStatusForUser($userId)
    {
        return $this->createQueryBuilder('d')
            ->select('COUNT (d.id) as total, d.state')
            ->innerJoin('d.user', 'u')
            ->where('u.id = :userId')
            ->setParameter(':userId', $userId)
            ->groupBy('d.state')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array $usersId
     * @return array
     */
    public function findCountByStatusForUsers(array $usersId)
    {
        $queryBuilder = $this->createQueryBuilder('d');

        $queryBuilder
            ->select('COUNT (d.id) as total, d.state')
            ->innerJoin('d.user', 'u');

        DoctrineQueryHelper::buildEqualOrInQuery($queryBuilder, 'id', $usersId, 'usersId', 'u');
        $queryBuilder->groupBy('d.state');

        return $queryBuilder->getQuery()->getResult();
    }
}
