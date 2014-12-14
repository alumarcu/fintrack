<?php
namespace Pms\FinanceBundle\Controller;

use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        $transactionsHelper = $transactionRepo->getTransactions();

        return $this->render(
            'PmsFinanceBundle:Dashboard:index.html.twig',
            array(
                'accounts' => $accounts,
                'scopes' => $scopes,
                'quickScopes' => $transactionsHelper->getScopes(),
                'transactions' => $transactionsHelper->getTransactions()
            )
        );
    }

    public function saveTransactionAction(Request $request) {
        $rawRequest = $request->getContent();
        var_dump($rawRequest);
        die("HERE");
        // Create Transaction and Transaction Lines
        // if there are no partials then create only one line
        // if there are parts, the amount not included will create a transaction line for main scope
    }
}
