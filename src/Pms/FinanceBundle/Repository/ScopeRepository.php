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

            }


        }// foreach

        return $qb;
    }

}