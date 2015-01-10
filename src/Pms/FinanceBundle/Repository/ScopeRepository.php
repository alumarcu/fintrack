<?php
namespace Pms\FinanceBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ScopeRepository extends EntityRepository
{
    public function getBuilderByFilters(array $filters = array())
    {
        $qb = $this->createQueryBuilder('s');

        foreach ($filters as $filterKey => $filterVal) {

            switch ($filterKey) {
                case 'name':
                case 'parent':
                    $qb->andWhere("s.{$filterKey} = :{$filterKey}")
                        ->setParameter($filterKey, $filterVal);
                    break;
            }
        }// foreach

        return $qb;
    }
}
