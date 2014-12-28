<?php
namespace Pms\FinanceBundle\Service;

use Doctrine\ORM\EntityManager;
use Pms\FinanceBundle\Entity\Scope;
use Pms\FinanceBundle\Entity\Transaction;
use Pms\FinanceBundle\Entity\TransactionLine;
use Pms\FinanceBundle\Exception\TransactionException;


class TransactionService
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    /**
     * Example: {"dest":"null","src":"1","mainScope":"casnice","mainValue":23,"dateOccurred":"20-12-2014","partials":[{"value":11,"scope":"alcool"}]}
     * @param array $transactionInfo
     * @throws \Pms\FinanceBundle\Exception\TransactionException
     */
    public function save(array $transactionInfo)
    {
        if ($transactionInfo['src'] === $transactionInfo['dest']) {
            throw new TransactionException(TransactionException::SAVE_REQUIRES_DISTINCT_SRC_DEST);
        }

        // Get references for accounts specified
        $refSource = null;
        $refDestination = null;

        if ($transactionInfo['src'] != "null") {
            $refSource = $this->em->getReference('PmsFinanceBundle:Account', $transactionInfo['src']);
        }
        if ($transactionInfo['dest'] != "null") {
            $refDestination = $this->em->getReference('PmsFinanceBundle:Account', $transactionInfo['dest']);
        }

        $dateRecorded = new \DateTime();
        $dateOccurred = \DateTime::createFromFormat('d-m-Y', $transactionInfo['dateOccurred']);

        if ($dateOccurred > $dateRecorded) {
            throw new TransactionException(TransactionException::SAVE_OCCURRED_IN_FUTURE);
        }

        $mainValue = (float) $transactionInfo['mainValue'];
        if (empty($mainValue)) {
            throw new TransactionException(TransactionException::SAVE_TRANSACTION_NO_VALUE);
        }

        // Create the new transaction
        $transaction = new Transaction();

        $transaction
            ->setDateRecorded($dateRecorded)
            ->setDateOccurred($dateOccurred);

        $transaction
            ->setSourceAccount($refSource)
            ->setDestinationAccount($refDestination);

        $this->em->persist($transaction);

        // Resolve the scopes
        /** @var \Pms\FinanceBundle\Entity\Scope $rootScope */
        $rootScope = null;

        // Find if the scope already exists
        $scopesRepository = $this->em->getRepository('PmsFinanceBundle:Scope');
        $mainScope = $scopesRepository->findBy(array('name' => $transactionInfo['mainScope']));

        // The parent scope of the main scope will be the root scope,
        // if the parent scope is null, than main scope = root scope
        if (!empty($mainScope) && is_array($mainScope)) {
            /** @var \Pms\FinanceBundle\Entity\Scope $mainScope */
            $mainScope = $mainScope[0];
            if ($mainScope->getParent() !== null) {
                $rootScope = $mainScope->getParent();
            } else {
                $rootScope = $mainScope;
            }
        } else {
            // Create new scope
            $mainScope = new Scope();
            $mainScope->setName($transactionInfo['mainScope']);
            $mainScope->setParent(null);
            $this->em->persist($mainScope);
            $rootScope = $mainScope;
        }

        // If there are no partials, will only add a single transaction line
        $partialSum = .0; // Total value of partials

        if (!empty($transactionInfo['partials']) && is_array($transactionInfo['partials'])) {
            foreach ($transactionInfo['partials'] as $partial) {
                // Check whether a scope with this name already exists
                $scope = $scopesRepository->findBy(array('name' => $partial['scope']));

                // If the scope exists but has a different parent from root scope,
                // it will keep its scope (it's considered okay to have partial from another root)
                if (!empty($scope) && is_array($scope)) {
                    $scope = $scope[0];
                } else {
                    // Create the new scope with root as parent
                    $scope = new Scope();
                    $scope->setName($partial['scope']);
                    $scope->setParent($rootScope);
                    $this->em->persist($scope);
                }

                $partialValue = (float) $partial['value'];

                // Ignore this partial when value is 0 or invalid
                if (empty($partialValue)) continue;

                $partialSum += $partialValue;

                $partialLine = new TransactionLine();
                $partialLine
                    ->setScope($scope)
                    ->setValue($partialValue)
                    ->setTransaction($transaction);

                $this->em->persist($partialLine);
            }
        }

        $finalMainValue = $mainValue - $partialSum;

        // Ignore the case whether finalMainValue is lower than 0, for now
        if ($finalMainValue > 0) {
            $transactionLine = new TransactionLine();
            $transactionLine
                ->setScope($mainScope)
                ->setValue($finalMainValue)
                ->setTransaction($transaction);

            $this->em->persist($transactionLine);
        }
    }
}
