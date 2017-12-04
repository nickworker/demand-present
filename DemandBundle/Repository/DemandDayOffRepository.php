<?php

namespace DemandBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use DemandBundle\Manager\DemandManager;
use AppBundle\Helper\DoctrineQueryHelper;

class DemandDayOffRepository extends DemandRepository
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
                ->select('d', 't')
                ->innerJoin('d.user', 'u')
                ->innerJoin('d.type', 't')
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
                ->select('d', 't', 'u')
                ->innerJoin('d.user', 'u')
                ->innerJoin('d.type', 't');

        DoctrineQueryHelper::buildEqualOrInQuery($queryBuilder, 'id', $usersId, 'usersId', 'u');

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
            switch ($key) {
                case DemandManager::FILTER_STATE:
                    DoctrineQueryHelper::buildEqualOrInQuery($queryBuilder, 'state', $val, $key, $alias);
                    continue;
            }
        }
    }
}
