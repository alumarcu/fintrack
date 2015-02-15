<?php
namespace Pms\FinanceBundle\Controller;

use Doctrine\ORM\Query;
use Pms\BaseBundle\Component\Http\ApiResponse;
use Pms\FinanceBundle\Entity\Account;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Pms\FinanceBundle\Exception\TransactionException;

class DashboardController extends Controller
{
    public function indexAction()
    {
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();
        /** @var $accountRepo \Pms\FinanceBundle\Repository\AccountRepository */
        $accountRepo = $em->getRepository('PmsFinanceBundle:Account');

        // Loads the dashboard template; module data is sent async
        return $this->render(
            'PmsFinanceBundle:Dashboard:index.html.twig',
            array(
                'accounts' => $accountRepo->getAccounts()
            )
        );
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function apiBalanceDataAction(Request $request)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var \Pms\BaseBundle\Service\UtilityService $util */
        $util = $this->container->get('pms_base.utility');
        /** @var \Pms\FinanceBundle\Repository\TransactionRepository $transactionRepo */
        $transactionRepo = $em->getRepository('PmsFinanceBundle:Transaction');
        /** @var \Pms\FinanceBundle\Repository\HistoryRepository $historyRepo */
        $historyRepo = $em->getRepository('PmsFinanceBundle:History');
        /** @var \Pms\BaseBundle\Component\Http\ApiResponse $response */
        $response = new ApiResponse();

        $return = array();
        try {
            $rawRequest = $request->getContent();
            $decodedRequest = $util->decode($rawRequest);

            if (empty($decodedRequest['account'])) {
                throw new TransactionException(TransactionException::LIST_BALANCE_NO_ACCOUNT);
            }
            $accountId = (int) $decodedRequest['account'];

            $accountRepo = $em->getRepository('PmsFinanceBundle:Account');

            /** @var \Pms\FinanceBundle\Entity\Account|null $account */
            $account = $accountRepo->find($accountId);
            if (is_null($account)) {
                throw new TransactionException(TransactionException::LIST_BALANCE_ACCOUNT_NOT_FOUND);
            }

            $extraFilters = array();
            if (!empty($decodedRequest['filters'])) {
                $extraFilters = $decodedRequest['filters'];
            }

            $lines = $transactionRepo->getTransactionsDaily($accountId, $extraFilters);
            $lines = array_merge_recursive($lines, $transactionRepo->getTransactionLineScopes($accountId, $extraFilters));

            $firstLine = end($lines);
            $firstLineDate = \DateTime::createFromFormat(Account::DATE_FORMAT, $firstLine['date']);
            reset($lines);

            $filters = array(
                'before' => $firstLineDate,
                'account' => $accountId
            );

            // todo - verify this logic, of providing the balance based on earlies shown date
            $builder = $historyRepo->getBuilderByFilters($filters);
            $builder->orderBy('h.date', 'desc');
            /** @var \Pms\FinanceBundle\Entity\History $historyLine */
            $historyLine = $builder->getQuery()->setMaxResults(1)->getOneOrNullResult();
            $lastBalance = is_null($historyLine) ? .0 : $historyLine->getValue();

            $lines = array_reverse($lines);
            foreach ($lines as &$line) {
                $isIncome = intval($line['isIncome']);
                $lastBalance += $isIncome ? $line['value'] : -$line['value'];
                $line['balance'] = $lastBalance;
            }

            $return['lines'] = array_reverse($lines);
            $return['account'] = array(
                'currency' => $account->getCurrency(),
                'name' => $account->getDisplayName(),
                'bank' => $account->getBankName(),
                'favorite' => $account->getIsFavorite()
            );

        } catch (\Exception $e) {
            return $response->failure(array($e->getMessage(), $e->getCode()));
        }

        return $response->success($return);
    }
}
