<?php
namespace Pms\FinanceBundle\Controller;

use Pms\BaseBundle\Component\Http\ApiResponse;
use Pms\FinanceBundle\Exception\TransactionException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TransactionController extends Controller
{
    public function apiSaveAction(Request $request)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var \Pms\BaseBundle\Service\UtilityService $util */
        $util = $this->container->get('pms_base.utility');
        /** @var \Pms\FinanceBundle\Service\TransactionService $transactionService */
        $transactionService = $this->container->get('pms_finance.transaction');
        /** @var \Pms\FinanceBundle\Service\HistoryService $historyService */
        $historyService = $this->container->get('pms_finance.history');

        /** @var \Pms\BaseBundle\Component\Http\ApiResponse $response */
        $response = new ApiResponse();

        try {
            $rawRequest = $request->getContent();
            $decodedRequest = $util->decode($rawRequest);

            $transactionService->save($decodedRequest);
            $em->flush();

            $historyService->save(array('account' => $decodedRequest['src']));
            $historyService->save(array('account' => $decodedRequest['dest']));
            $em->flush();

        } catch (TransactionException $e) {
            return $response->failure(array($e->getMessage(), $e->getCode()));
        }

        return $response->success();
    }

    public function apiFormDataAction(Request $request)
    {
        /** @var \Pms\BaseBundle\Component\Http\ApiResponse $response */
        $response = new ApiResponse();

        $payload = array(
            'accounts' => $this->getAccounts(),
            'scopes' => $this->getScopes()
        );


        return $response->success($payload);
    }

    protected function getAccounts()
    {
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();
        /** @var $accountRepo \Pms\FinanceBundle\Repository\AccountRepository */
        $accountRepo = $em->getRepository('PmsFinanceBundle:Account');
        $accountsBuilder = $accountRepo->getBuilderByFilters();
        $accountsBuilder->select('a.id, a.displayName, a.bankName, a.currency, a.isFavorite');

        return $accountsBuilder->getQuery()->getResult();
    }

    protected function getScopes()
    {
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();
        /** @var $scopeRepo \Pms\FinanceBundle\Repository\ScopeRepository */
        $scopeRepo = $em->getRepository('PmsFinanceBundle:Scope');
        $scopeBuilder = $scopeRepo->getBuilderByFilters();
        $scopeBuilder->select('s.id, s.name');

        return $scopeBuilder->getQuery()->getResult();
    }
}
