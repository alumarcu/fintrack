<?php
namespace Pms\FinanceBundle\Controller;

use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    public function indexAction()
    {
        return $this->render('PmsFinanceBundle:Dashboard:index.html.twig');
    }

    public function formTransactionAction(Request $request)
    {
        // TODO: UI: Only when partialScopes are added recent Partial scopes panel is visible

        // @todo: Load data required for the transaction form
    }

    public function tableTransactionsAction(Request $request)
    {
        /** @var $transactionRepo \Pms\FinanceBundle\Repository\TransactionRepository */
        //$transactionRepo = $em->getRepository('PmsFinanceBundle:Transaction');
        /** @var $transactionsHelper \Pms\FinanceBundle\Helper\BalanceHelper */
        $transactionsHelper = $transactionRepo->getTransactions();

        // @todo: Load data required for the recent transactions table based on front-end provided filters
    }

}
