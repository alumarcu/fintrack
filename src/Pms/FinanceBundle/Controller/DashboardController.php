<?php
namespace Pms\FinanceBundle\Controller;

use Doctrine\ORM\Query;
use Pms\BaseBundle\Component\Http\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Pms\FinanceBundle\Exception\TransactionException;

class DashboardController extends Controller
{
    public function indexAction()
    {
        // Loads the dashboard template; module data is sent async
        return $this->render('PmsFinanceBundle:Dashboard:index.html.twig');
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

            $return['lines'] = $lines;
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
