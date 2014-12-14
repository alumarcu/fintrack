<?php
namespace Pms\FinanceBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Pms\FinanceBundle\Helper\BalanceHelper;

class TransactionRepository extends EntityRepository
{
    public function getBuilderByFilters(array $filters = array())
    {
        $qb = $this->createQueryBuilder('t');
        foreach ($filters as $filterKey => $filterVal) {
            switch ($filterKey) {

            }
        }// foreach

        return $qb;
    }

    public function getTransactions(array $options = array(), array $filters = array())
    {
        $defaultFilters = array(

        );
        $filters = array_merge($defaultFilters, array_filter($filters));

        $defaultOptions = array(
            'transaction_days_merge' => true,
            'transaction_days_per_account_limit' => 10, // mandatory
            'return_scopes' => 'frequent',  // "frequent"|"recent"|null
            'return_scopes_limit' => 10,    // int|null

        );
        $options = array_merge($defaultOptions, array_filter($options));

        $qb = $this->getBuilderByFilters($filters);
        $qb->select(
            'IDENTITY(t.sourceAccount) AS sourceAccount',
            'IDENTITY(t.destinationAccount) AS destinationAccount',
            't.dateOccurred'
        );
        $qb->orderBy('t.dateOccurred', 'DESC');

        $query = $qb->getQuery();
        $transactionRows = $query->getResult();

        $balanceHelper = new BalanceHelper($transactionRows);
        $balanceHelper->process();

        if ($options['transaction_days_merge']) {
            $balanceHelper->balanceMergePerDay();
        }
        if ($limit = $options['transaction_days_per_account_limit']) {
            $balanceHelper->balanceDaysPerAccountLimit($limit);
        }
        if ($returnScopes = $options['return_scopes']) {
            $limit = $options['return_scopes_limit'];
            switch ($returnScopes) {
                case 'frequent':
                    $balanceHelper->findMostFrequentScopes($limit);
                    break;
                case 'recent':
                    $balanceHelper->findMostFrequentScopes($limit);
                    break;
            }
        }
        return $balanceHelper->finish();
    }
}
