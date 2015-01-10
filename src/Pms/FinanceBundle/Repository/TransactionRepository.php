<?php
namespace Pms\FinanceBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Pms\FinanceBundle\Entity\Account;

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
                    break;
                case 'occurredAfterOrEqual':
                    $qb->andWhere("t.dateOccurred >= :{$filterKey}")
                        ->setParameter($filterKey, $filterVal);
                    break;
                case 'occurredBefore':
                    $qb->andWhere("t.dateOccurred < :{$filterKey}")
                        ->setParameter($filterKey, $filterVal);
                    break;
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

    public function getTransactionsDaily($account, array $extraFilters = array())
    {
        $filters = array(
            'ijLines' => true,
            'containsAccount' => $account
        );

        // Can include additional filters sent directly from the ui, check the js module
        $filters = array_merge($filters, $extraFilters);

        $builder = $this->getBuilderByFilters($filters);
        $builder->select(
            "t.dateOccurred AS dateOccurred",
            "SUM(tl.value) AS value",
            "(CASE WHEN t.sourceAccount = {$account} THEN 0 ELSE 1 END) AS income"
            // When above is 1, t.destinationAccount is $account, as per query
        );

        $builder
            ->addGroupBy('t.dateOccurred')
            ->addGroupBy('t.sourceAccount');

        // Retreive transactions as aggregated in the above query
        $transactions = $builder->getQuery()->getResult();
        // Retreive stored preferences (ideally, from session)
        $dateFormat = Account::DATE_FORMAT;

        // Create the lines of the balance table;
        $lines = array();
        foreach ($transactions as $trans) {
            /** @var \DateTime $occurred */
            $occurred = $trans['dateOccurred'];
            $key = $this->createTransactionsDailyLineKey($occurred, $trans['income']);
            $lines[$key] = array(
                'date' => $occurred->format($dateFormat),
                'value' => $trans['value'],
                'isIncome' => $trans['income']
            );
        }

        return $lines;
    }

    public function getTransactionLineScopes($account, array $extraFilters = array())
    {
        $filters = array(
            'ijLines' => true,
            'ijScopes' => true,
            'containsAccount' => $account
        );
        // Can include additional filters sent directly from the ui, check the js module
        $filters = array_merge($filters, $extraFilters);

        // TODO: Can also use this to get expenses by scope or by day (should be option-based)
        $builder = $this->getBuilderByFilters($filters);
        $builder->select(
            't.dateOccurred AS dateOccurred',
            'tls.name AS scope',
            '(CASE WHEN t.sourceAccount = 1 THEN 0 ELSE 1 END) AS income'
            // When above is 1, t.destinationAccount is $account, as per query
        );

        // Note there's no way to group the lines here, until we implement something like GROUP_CONCAT
        $lineScopes = $builder->getQuery()->getResult();

        $lines = array();

        foreach ($lineScopes as $ls) {
            /** @var \DateTime $occurred */
            $occurred = $ls['dateOccurred'];
            $key = $this->createTransactionsDailyLineKey($occurred, $ls['income']);
            if (!array_key_exists($key, $lines)) {
                $lines[$key] = array();
            }
            if (!array_key_exists('scopes', $lines[$key])) {
                $lines[$key]['scopes'] = array();
            }

            // Make sure the scope is unique in the array
            if (!in_array($ls['scope'], $lines[$key]['scopes'])) {
                $lines[$key]['scopes'][] = $ls['scope'];
            }
        }

        return $lines;
    }

    protected function createTransactionsDailyLineKey(\DateTime $date, $type)
    {
        return $date->format('Ymd') . '-' . $type;
    }

    public function getFrequentScopes(array $options = array())
    {
        if (!isset($options['scopes_size'])) {
            $options['scopes_size'] = 15;
        }

        $filters = array(
            'ijLines' => true,
            'ijScopes' => true
        );

        $builder = $this->getBuilderByFilters($filters);
        $builder->select('tls.name AS scope, tl.id AS transaction');

        $lines = $builder->getQuery()->getResult();
        $counter = array();
        foreach ($lines as $line) {
            if (!array_key_exists($line['scope'], $counter)) {
                $counter[$line['scope']] = 0;
            }
            $counter[$line['scope']]++;
        }

        arsort($counter);
        if (count($counter) > $options['scopes_size']) {
            return array_slice(array_keys($counter), 0, $options['scopes_size']);
        }

        return array_keys($counter);
    }

    public function getRecentScopes(array $options = array())
    {
        if (!isset($options['days_before'])) {
            $options['days_before'] = 7;
        }

        $date = new \DateTime();
        $date->modify(sprintf('-%d days', $options['days_before']));

        $filters = array(
            'ijLines' => true,
            'ijScopes' => true,
            'occurredAfter' => $date->format('Y-m-d')
        );

        $builder = $this->getBuilderByFilters($filters);
        $builder->select('tls.name AS scope')
            ->groupBy('scope')
            ->orderBy('t.dateOccurred', 'desc');

        $lines = $builder->getQuery()->getResult();
        $scopes = array();
        foreach ($lines as $line) {
            $scopes[] = $line['scope'];
        }

        return $scopes;
    }
}
