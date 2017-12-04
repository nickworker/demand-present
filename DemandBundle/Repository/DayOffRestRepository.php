<?php

namespace DemandBundle\Repository;

use DemandBundle\Entity\DayOffRest;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use DemandBundle\Manager\DayOffRestManager;
use Doctrine\DBAL\Types\Type;

class DayOffRestRepository extends EntityRepository
{
    /**
     * @param array $params
     * @return \Doctrine\ORM\Query
     */
    public function findForListQuery(array $params = [])
    {
        $queryBuilder = $this->createQueryBuilder('d');

        $queryBuilder
            ->innerJoin('d.user', 'u')
            ->innerJoin('d.type', 't');

        if (isset($params[DayOffRestManager::FILTER]) && count($params[DayOffRestManager::FILTER])) {
            $this->applyFilter($queryBuilder, $params[DayOffRestManager::FILTER]);
        }

        return $queryBuilder->getQuery();
    }

    /**
     * @param \DateTime $date
     * @param string $status
     * @param bool|true $isAuto
     * @return array
     */
    public function findForAutoUpdate(\DateTime $date, $status = DayOffRest::STATUS_ACTIVE, $isAuto = true)
    {
        $queryBuilder = $this->createQueryBuilder('d');

        return $queryBuilder
            ->innerJoin('d.type', 't')
            ->where($queryBuilder->expr()->isNotNull('d.autoDate'))
            ->andWhere('d.autoDate = :autoDate')
            ->andWhere('t.isAuto = :isAuto')
            ->andWhere('d.status = :status')
            ->setParameter(':autoDate', $date, Type::DATE)
            ->setParameter(':isAuto', $isAuto)
            ->setParameter(':status', $status)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $userId
     * @param $status
     * @param bool|false $isAuto
     * @param bool|false $isDisabled
     * @return array
     */
    public function findByAutoAndDisabledMode($userId, array $statuses, $isAuto = false, $isDisabled = false)
    {
        $queryBuilder = $this->createQueryBuilder('d');

        return $queryBuilder
            ->innerJoin('d.type', 't')
            ->innerJoin('d.user', 'u')
            ->where('u.id = :userId')
            ->andWhere($queryBuilder->expr()->in('d.status', ':statuses'))
            ->andWhere('t.isAuto = :isAuto')
            ->andWhere('t.isDisabled = :isDisabled')
            ->setParameter(':userId', $userId)
            ->setParameter(':statuses', $statuses)
            ->setParameter(':isAuto', $isAuto)
            ->setParameter(':isDisabled', $isDisabled)
            ->getQuery()
            ->getResult();
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
                case DayOffRestManager::FILTER_USER:
                    $queryBuilder
                        ->andWhere($queryBuilder->expr()->in('u.id', sprintf(':%s', $key)))
                        ->setParameter(sprintf(':%s', $key), $val);
                    continue;
                case DayOffRestManager::FILTER_TYPE:
                    $queryBuilder
                        ->andWhere($queryBuilder->expr()->in('t.id', sprintf(':%s', $key)))
                        ->setParameter(sprintf(':%s', $key), $val);
                    continue;
                case DayOffRestManager::FILTER_STATUS:
                    $queryBuilder
                        ->andWhere($queryBuilder->expr()->in(sprintf('%s.status', $alias), sprintf(':%s', $key)))
                        ->setParameter(sprintf(':%s', $key), $val);
                    continue;
                case DayOffRestManager::FILTER_IS_AUTO_DATE:
                    $exp = $val == DayOffRestManager::FILTER_YES
                            ? $queryBuilder->expr()->isNotNull(sprintf('%s.autoDate', $alias))
                            : $queryBuilder->expr()->isNull(sprintf('%s.autoDate', $alias));

                    $queryBuilder->andWhere($exp);
                    continue;
                case DayOffRestManager::FILTER_AUTO_DATE:
                    $queryBuilder
                        ->andWhere($queryBuilder->expr()->andX(
                            $queryBuilder->expr()->isNotNull(sprintf('%s.autoDate', $alias)),
                            $queryBuilder->expr()->eq(sprintf('%s.autoDate', sprintf(':%s', $key)))
                        ))
                        ->setParameter(sprintf(':%s', $key), $val, Type::DATE);
                    continue;
            }
        }
    }
}
