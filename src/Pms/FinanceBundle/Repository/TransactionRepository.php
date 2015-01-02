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
                case 'sourceAccount':
                case 'destinationAccount':
                    $qb->andWhere("t.{$filterKey} = :{$filterKey}")
                        ->setParameter($filterKey, $filterVal);
                    break;
                case 'containsAccount':
                    $qb->orWhere("t.sourceAccount = :{$filterKey}")
                        ->orWhere("t.destinationAccount = :{$filterKey}")
                        ->setParameter($filterKey, $filterVal);
                    break;
                case 'occurredAfter':
                    $qb->andWhere("t.dateOccurred > :{$filterKey}")
                        ->setParameter($filterKey, $filterVal);
                case 'occurredAfterOrEqual':
                    $qb->andWhere("t.dateOccurred >= :{$filterKey}")
                        ->setParameter($filterKey, $filterVal);
                    break;
                case 'occurredBefore':
                    $qb->andWhere("t.dateOccurred < :{$filterKey}")
                        ->setParameter($filterKey, $filterVal);
                case 'ijLines':
                    $qb->innerJoin('PmsFinanceBundle:TransactionLine', 'tl', 'WITH', 't.id = tl.transaction');
                    break;
                case 'ijScopes':
                    $qb->leftJoin('tl.scope', 'tls');
                    break;
            }
        }// foreach

        return $qb;
    }

    public function getTransactions(array $options = array())
    {
        if (!isset($options['filters'])) {
            $options['filters'] = array(
                'containsAccount' => 1,
                'ijLines' => true,

            );
        }

        $builder = $this->getBuilderByFilters(array_filter($options['filters']));
        $builder->select(
            'IDENTITY(t.sourceAccount) AS sourceAccount',
            'IDENTITY(t.destinationAccount) AS destinationAccount',
            't.dateOccurred',
            'SUM(tl.value) as value'

        );

        $builder->addGroupBy('t.dateOccurred')
            ->addGroupBy('t.sourceAccount');

        $transactions = $builder->getQuery()->getResult();

        //var_dump($transactions);


    }

    /**
     * @deprecated
     */
    public function getTransactionsOld(array $options = array(), array $filters = array())
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
