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

    public function getAccounts(array $filters = array())
    {
        $qb = $this->getBuilderByFilters($filters);
        $qb->select('a.id, a.displayName, a.bankName, a.currency, a.isFavorite');

        return $qb->getQuery()->getResult();
    }

}