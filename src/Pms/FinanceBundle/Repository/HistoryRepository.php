<?php
namespace Pms\FinanceBundle\Repository;

use Doctrine\ORM\EntityRepository;

class HistoryRepository extends EntityRepository
{
    public function getBuilderByFilters(array $filters = array())
    {
        $qb = $this->createQueryBuilder('h');

        foreach ($filters as $filterKey => $filterVal) {

            switch ($filterKey) {
                case 'account':
                    $qb->andWhere("h.{$filterKey} = :{$filterKey}")
                        ->setParameter($filterKey, $filterVal);
                    break;
            }


        }// foreach

        return $qb;
    }
}