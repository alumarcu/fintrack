<?php
namespace Pms\FinanceBundle\Service;

use Doctrine\ORM\EntityManager;
use Pms\FinanceBundle\Entity\History;

class HistoryService
{
    const MIN_DAYS_BEFORE_UDATE = 5;

    protected $em;

    /** @var null|\DateTime */
    private $now;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->now = null;
    }

    /**
     * Example: {"account":"2"}
     * @param array $info
     */
    public function save(array $info)
    {
        if (empty($info['account'])) {
            return;
        }

        $accountId = $info['account'];

        $this->now = $this->now ?: new \DateTime();

        /** @var \Pms\FinanceBundle\Repository\HistoryRepository $historyRepository */
        $historyRepository = $this->em->getRepository('PmsFinanceBundle:History');

        $historyBuilder = $historyRepository->getBuilderByFilters(array('account' => $accountId));
        $historyBuilder
            ->select('h.id, h.date, h.value, IDENTITY(h.account) AS account')
            ->orderBy('h.date', 'DESC')
            ->setMaxResults(1);

        $historyLine = $historyBuilder->getQuery()->getResult();

        if (!empty($historyLine)) {
            $historyLine = $historyLine[0]; // Last history line, as queried
            /** @var \DateTime $histDate */
            $histDate = $historyLine['date'];
            /** @var \DateInterval $histDiff */
            $histDiff = $this->now->diff($histDate);

            if ($histDiff->days > self::MIN_DAYS_BEFORE_UDATE) {
                $history = $this->updateHistory($accountId, $histDate, $historyLine['value']);
                $this->em->persist($history);
            } // Otherwise there is no need for an update
        } else {
            // History needs to be initialized
            $history = $this->updateHistory($accountId);
            $this->em->persist($history);
        }
    }

    protected function updateHistory($accountId, \DateTime $dateAfter = null, $previous = .0)
    {
        /** @var \Pms\FinanceBundle\Repository\TransactionRepository $transactionRepository */
        $transactionRepository = $this->em->getRepository('PmsFinanceBundle:Transaction');

        $filters = array(
            'containsAccount' => $accountId,
            'occurredBefore' => $this->now->format('Y-m-d') // Must not include current day!
        );

        if (!empty($dateAfter)) {
            $filters['occurredAfterOrEqual'] = $dateAfter->format('Y-m-d');
        }

        $transactionBuilder = $transactionRepository->getBuilderByFilters($filters);
        /** @var \Pms\FinanceBundle\Entity\Transaction[] $transactions */
        $transactions = $transactionBuilder->getQuery()->getResult();

        $balanceAmount = .0;
        foreach ($transactions as $transaction) {
            $isDestination = !is_null($transaction->getDestinationAccount()); // Meaning, this is income
            foreach ($transaction->getLines() as $line) {
                /** @var \Pms\FinanceBundle\Entity\TransactionLine $line */
                $balanceAmount += ($isDestination) ? ($line->getValue()) : (-$line->getValue());
            }
        }

        $balance = $previous + $balanceAmount;

        $history = new History();
        $history
            ->setAccount($this->em->getReference('PmsFinanceBundle:Account', $accountId))
            ->setDate($this->now)
            ->setValue($balance);

        return $history;
    }

    public function setNow(\DateTime $date)
    {
        $this->now = $date;
    }
}
