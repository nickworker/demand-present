<?php

namespace DemandBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use DemandBundle\Manager\DayOffTypeManager;

class DayOffTypeRepository extends EntityRepository
{
    /**
     * @param $companyId
     * @param array $params
     * @return \Doctrine\ORM\Query
     */
    public function findForCompanyListQuery($companyId, array $params = [])
    {
        $queryBuilder = $this->createQueryBuilder('d');

        $queryBuilder
            ->innerJoin('d.owner', 'o')
            ->innerJoin('o.company', 'c')
            ->where('c.id = :companyId')
            ->setParameter(':companyId', $companyId);

        if (isset($params[DayOffTypeManager::FILTER]) && count($params[DayOffTypeManager::FILTER])) {
            $this->applyFilter($queryBuilder, $params[DayOffTypeManager::FILTER]);
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
                case DayOffTypeManager::FILTER_IS_AUTO:
                    $queryBuilder
                        ->andWhere(sprintf('%s.isAuto = :%s', $alias, $key))
                        ->setParameter(sprintf(':%s', $key), $val == DayOffTypeManager::FILTER_YES);
                    continue;
                case DayOffTypeManager::FILTER_IS_DISABLED:
                    $queryBuilder
                        ->andWhere(sprintf('%s.isDisabled = :%s', $alias, $key))
                        ->setParameter(sprintf(':%s', $key), $val == DayOffTypeManager::FILTER_YES);
                    continue;
            }
        }
    }
}
