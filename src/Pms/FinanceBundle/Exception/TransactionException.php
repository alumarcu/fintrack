<?php
namespace Pms\FinanceBundle\Exception;

use Pms\BaseBundle\Exception\TranslatedException;

class TransactionException extends TranslatedException
{
    const SAVE_REQUIRES_DISTINCT_SRC_DEST = 1;
    const SAVE_OCCURRED_IN_FUTURE = 2;
    const SAVE_TRANSACTION_NO_VALUE = 3;

    const LIST_BALANCE_NO_ACCOUNT = 11;
    const LIST_BALANCE_ACCOUNT_NOT_FOUND = 12;

    protected function getTranslationPrefix()
    {
        return 'translation.exception.';
    }

    protected function getErrorMessages($pre = '')
    {
        return array(
            self::SAVE_REQUIRES_DISTINCT_SRC_DEST => array(
                "{$pre}save_requires_distinct_src_dest",
                "Source account same as destination"
            ),
            self::SAVE_OCCURRED_IN_FUTURE => array(
                "{$pre}save_occurred_in_future",
                "Date occurred cannot be in the future"
            ),
            self::SAVE_TRANSACTION_NO_VALUE => array(
                "{$pre}save_transaction_no_value",
                "Transaction has no value"
            ),
            self::LIST_BALANCE_NO_ACCOUNT => array(
                "{$pre}list_balance_no_account",
                "Missing account id for which to retreive balance lines"
            ),
            self::LIST_BALANCE_ACCOUNT_NOT_FOUND => array(
                "{$pre}list_balance_account_not_found",
                "Account id not found"
            )
        );
    }
}