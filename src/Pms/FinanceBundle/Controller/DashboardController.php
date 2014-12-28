<?php
namespace Pms\FinanceBundle\Controller;

use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
}
