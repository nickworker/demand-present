<?php

namespace DemandBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use DemandBundle\Manager\DemandManager;

class DemandPaymentRepository extends DemandRepository
{
    /**
     * @param $userId
     * @param array $params
     * @return \Doctrine\ORM\Query
     */
    public function getByUserQuery($userId, array $params = [])
    {
        $queryBuilder = $this->createQueryBuilder('d');

        $queryBuilder
            ->select('d')
            ->innerJoin('d.user', 'u')
            ->where('u.id = :userId')
            ->setParameter(':userId', $userId);

        if (isset($params[DemandManager::FILTER])) {
            $this->applyFilter($queryBuilder, $params[DemandManager::FILTER]);
        }

        return $queryBuilder->getQuery();
    }

    /**
     * @param array $usersId
     * @param array $params
     * @return \Doctrine\ORM\Query
     */
    public function getByUsersIdQuery(array $usersId, array $params = [])
    {
        $queryBuilder = $this->createQueryBuilder('d');

        $queryBuilder
            ->select('d', 'u')
            ->innerJoin('d.user', 'u')
            ->where($queryBuilder->expr()->in('u.id', ':usersId'))
            ->setParameter(':usersId', $usersId);

        if (isset($params[DemandManager::FILTER])) {
            $this->applyFilter($queryBuilder, $params[DemandManager::FILTER]);
        }

        return $queryBuilder->getQuery();
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $params
     */
    private function applyFilter(QueryBuilder $queryBuilder, array $params)
    {
        $aliases    = $queryBuilder->getRootAliases();
        $alias      = reset($aliases);

        foreach ($params as $key => $val) {
            if ($key == DemandManager::FILTER_STATE) {
                if (count($val) > 1) {
                    $queryBuilder
                        ->andWhere($queryBuilder->expr()->in(sprintf('%s.state', $alias), sprintf(':%s', $key)))
                        ->setParameter(sprintf(':%s', $key), $val);
                } else {
                    $queryBuilder
                        ->andWhere(sprintf('%s.state = :%s', $alias, $key))
                        ->setParameter(sprintf(':%s', $key), $val);
                }
            }
        }
    }

    /**
     * @param $userId
     * @return QueryBuilder
     */
    public function getCountByStatusForUser($userId)
    {
        $queryBuilder = $this->createQueryBuilder('d');

        return $queryBuilder
            ->select('d')
            ->innerJoin('d.user', 'u')
            ->where('u.id = :userId')
            ->setParameter(':userId', $userId);
    }
}
