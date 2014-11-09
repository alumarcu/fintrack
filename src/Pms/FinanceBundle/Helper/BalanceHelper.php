<?php
namespace Pms\FinanceBundle\Helper;

use Pms\BaseBundle\Helper\RepositoryHelper;

class BalanceHelper extends RepositoryHelper
{
    const TRANSACT_EXPENSE_KEY = 'sourceAccount';
    const TRANSACT_INCOME_KEY = 'destinationAccount';
    const DATE_FORMAT = 'd-m-Y';

    protected $transactions = array();
    protected $scopes = array();

    public function process()
    {
        if ($this->haveStateAbove(self::STATE_NOT_STARTED)) {
            return $this;
        } else if ($this->haveState(self::STATE_FINISHED)) {
            return null;
        }

        $this->transactions = array();
        foreach ($this->dataset as $transaction) {
            foreach (array(self::TRANSACT_EXPENSE_KEY, self::TRANSACT_INCOME_KEY) as $transactionKey) {
                $this->processTransaction($transaction, $transactionKey);
            }
        }
        $this->state = self::STATE_PROCESSED;
        return $this;
    }

    public function balanceMergePerDay()
    {
        if ($this->haveStateBelow(self::STATE_PROCESSED)) {
            return null;
        }

        foreach ($this->transactions as $account => $transactionDates) {
            foreach ($transactionDates as $date => $transactions) {
                $value = 0.0;
                $scopes = [];
                foreach ($transactions as $transItem) {
                    $value += $transItem['value'];
                    !empty($transItem['scope']) ? $scopes[] = $transItem['scope'] : null;
                }
                $this->transactions[$account][$date] = array (
                    'value' => $value,
                    'scopes' => array_unique($scopes)
                );
            }
        }

        return $this;
    }

    public function balanceDaysPerAccountLimit($limit)
    {
        if ($this->haveStateBelow(self::STATE_PROCESSED)) {
            return null;
        }

        foreach ($this->transactions as $account => $transactionDates) {
            $this->transactions[$account] = array_slice($this->transactions[$account], 0, $limit, true);
        }
        return $this;
    }

    public function findMostFrequentScopes($limit)
    {
        if ($this->haveStateBelow(self::STATE_NOT_STARTED)) {
            return null;
        }

        $counter = array();
        foreach ($this->dataset as $transaction) {
            $scope = $transaction['scope'];
            if (empty($scope)) {
                continue;
            }
            if (!isset($counter[$scope])) {
                $counter[$scope] = 0;
            }
            $counter[$scope]++;
        }
        arsort($counter, SORT_NUMERIC);
        $this->scopes = array_keys(array_slice($counter, 0, $limit, true));

        return $this;
    }

    public function findMostRecentScopes($limit)
    {
        if ($this->haveStateBelow(self::STATE_PROCESSED)) {
            return null;
        }
        $scopes = array();
        foreach ($this->dataset as $transaction) {
            $scope = $transaction['scope'];
            if (empty($scope)) {
                continue;
            }
            /** @var $date \DateTime */
            $date = $transaction['dateOccurred'];
            $date = $date->format('U');
            if (!isset($scopes[$scope]) || $scopes[$scope] < $date) {
                $scopes[$scope] = $date;
            }
        }
        arsort($scopes, SORT_NUMERIC);
        $this->scopes = array_keys(array_slice($scopes, 0, $limit, true));

        return $this;
    }

    public function getTransactions()
    {
        if (!$this->haveState(self::STATE_FINISHED)) {
            return null;
        }
        return $this->transactions;
    }

    public function getScopes()
    {
        if (!$this->haveState(self::STATE_FINISHED)) {
            return null;
        }
        return $this->scopes;
    }

    protected function processTransaction($row, $transactionKey)
    {
        $account = $row[$transactionKey];
        if ($account == 'NULL' || empty($account)) {
            return;
        }

        /** @var $dateOccurred \DateTime */
        $dateOccurred = $row['dateOccurred'];
        if (!isset($this->transactions[$account])) {
            $this->transactions[$account] = array();
        }
        $this->transactions[$account][$dateOccurred->format(self::DATE_FORMAT)][] = array(
            'value' => ($transactionKey == self::TRANSACT_EXPENSE_KEY) ? (0 - $row['value']) : $row['value'],
            'scope' => $row['scope'],
        );
    }
}
