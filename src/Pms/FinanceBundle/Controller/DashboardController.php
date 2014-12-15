<?php
namespace Pms\FinanceBundle\Controller;

use Doctrine\ORM\Query;
use Pms\FinanceBundle\Entity\Scope;
use Pms\FinanceBundle\Entity\Transaction;
use Pms\FinanceBundle\Entity\TransactionLine;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    public function indexAction()
    {
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();

        /** @var $accountRepo \Pms\FinanceBundle\Repository\AccountRepository */
        $accountRepo = $em->getRepository('PmsFinanceBundle:Account');
        $accountsBuilder = $accountRepo->getBuilderByFilters(array());
        $accountsBuilder->select(
            'a.id',
            'a.displayName',
            'a.bankName',
            'a.currency',
            'a.isFavorite'
        );
        $accounts = $accountsBuilder->getQuery()->getResult();

        /** @var $scopeRepo \Pms\FinanceBundle\Repository\ScopeRepository */
        $scopeRepo = $em->getRepository('PmsFinanceBundle:Scope');
        $scopeBuilder = $scopeRepo->getBuilderByFilters();
        $scopeBuilder->select(
            's.id',
            's.name'
        );
        $scopes = $scopeBuilder->getQuery()->getResult();

        /** @var $transactionRepo \Pms\FinanceBundle\Repository\TransactionRepository */
        $transactionRepo = $em->getRepository('PmsFinanceBundle:Transaction');
        /** @var $transactionsHelper \Pms\FinanceBundle\Helper\BalanceHelper */
        //$transactionsHelper = $transactionRepo->getTransactions();

        // TODO: Split into partialScopes and mainScopes
        // TODO: UI: Only when partialScopes are added recent Partial scopes panel is visible
        return $this->render(
            'PmsFinanceBundle:Dashboard:index.html.twig',
            array(
                'accounts' => $accounts,
                'scopes' => $scopes,
                'quickScopes' => array(),//$transactionsHelper->getScopes(),
                'transactions' => array(),//$transactionsHelper->getTransactions()
            )
        );
    }

    public function saveTransactionAction(Request $request) {
        // TODO: rename /async/ to /api/
        // TODO: create an api response class
        $response = array(
            'isError' => false,
            'messages' => array(),
            'results' => array()
        );
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $rawRequest = $request->getContent();
        // TODO: create an utility class and make a symfony based decode function
        $request = json_decode($rawRequest, true);

        if ($request['src'] == $request['dest']) {
            $response['isError'] = true;
            $response['messages'][] = 'Source account same as destination!';
            return new JsonResponse($response, 500);
        }
        $sourceRef = null;
        $destinationRef = null;
        if ($request['src'] != "null") {
            $sourceRef = $em->getReference('PmsFinanceBundle:Account', $request['src']);
        }
        if ($request['dest'] != "null") {
            $destinationRef = $em->getReference('PmsFinanceBundle:Account', $request['dest']);
        }

        // TODO: Defensive programming if possible?
        $transaction = new Transaction();
        $transaction->setDateRecorded(new \DateTime());
        $transaction->setDateOccurred(\DateTime::createFromFormat('d-m-Y', $request['dateOccurred']));
        $transaction->setSourceAccount($sourceRef);
        $transaction->setDestinationAccount($destinationRef);
        $em->persist($transaction);

        // TODO: Insert partial scopes as transaction lines
        // create scope if it does not exist
        $scopesRepository = $em->getRepository('PmsFinanceBundle:Scope');
        // find existing scope
        $mainScope = $scopesRepository->findBy(array('name' => $request['mainScope']));

        if (!empty($mainScope) && is_array($mainScope)) {
            $mainScope = $mainScope[0];
        } else {
            // create it
            $mainScope = new Scope();
            $mainScope->setName($request['mainScope']);
            $mainScope->setParent(null);
            $em->persist($mainScope);
        }
        //todo: validate that mainScope is a root scope ?? (has no parent)



        // TODO: Create Transaction Lines
        $partialSum = .0;
        if (!empty($request['partials']) && is_array($request['partials'])) {
            foreach ($request['partials'] as $partial) {
                $scope = $scopesRepository->findBy(array('name' => $partial['scope']));

                if (!empty($scope) && is_array($scope)) {
                    $scope = $scope[0];
                } else {
                    // create it
                    $scope = new Scope();
                    $scope->setName($partial['scope']);
                    $scope->setParent($mainScope);
                    $em->persist($scope);
                }
                $partialValue = (float) $partial['value'];
                $partialSum += $partialValue;

                $partialLine = new TransactionLine();
                $partialLine->setScope($scope);
                $partialLine->setValue($partialValue); //todo: validate this
                $partialLine->setTransaction($transaction);
                $em->persist($partialLine);
            }
        }
        $mainValue = (float) $request['mainValue'] - $partialSum;

        $transactionLine = new TransactionLine();
        $transactionLine->setScope($mainScope);
        $transactionLine->setValue($mainValue); //todo: validate this
        $transactionLine->setTransaction($transaction);
        $em->persist($transactionLine);

        // if there are no partials then create only one line
        // if there are parts, the amount not included will create a transaction line for main scope



        // TODO: cache all transactions on a separate Balance table and update it here
        $em->flush();

        var_dump($request);
        die("HERE");


    }
}
