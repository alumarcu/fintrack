<?php
namespace Pms\FinanceBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AccountRepository extends EntityRepository
{
    public function getBuilderByFilters(array $filters = array())
    {
        $qb = $this->createQueryBuilder('a');

        foreach ($filters as $filterKey => $filterVal) {

            switch ($filterKey) {

            }


        }// foreach

        return $qb;
    }

}