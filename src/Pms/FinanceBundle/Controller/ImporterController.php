<?php
namespace Pms\FinanceBundle\Controller;

use Pms\FinanceBundle\Entity\Balance;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ImporterController extends Controller
{
    public function indexAction(Request $request)
    {
        $requestData = $request->request->all();
        $templateVars = array(
            'importerContent' => ''
        );

        if (!empty($requestData['importerData'])) {
            /** @var $em \Doctrine\ORM\EntityManager */
            $em = $this->getDoctrine()->getManager();

            $rowsToImport = json_decode($requestData['importerData']);
            if (!is_null($rowsToImport)) {
                $this->importRows($rowsToImport);
                $em->flush();
            } else {
                $templateVars['importerContent'] = empty($postData['importerContent']) ? '' : $postData['importerContent'];
            }
        }

        return $this->render('PmsFinanceBundle:Importer:index.html.twig', $templateVars);
    }

    protected function importRows($rows)
    {
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();
        foreach ($rows as $row) {
            $types = array('income', 'expense');
            foreach ($types as $type) {
                if (!empty($row->$type)) {
                    // Create an income entry
                    $balance = new Balance();
                    $balanceType = $type == 'income' ? Balance::BALANCE_TYPE_INCOME : Balance::BALANCE_TYPE_EXPENSE;
                    $balanceScope = $type == 'income' ? Balance::BALANCE_SCOPE_UNREG_INCOME : Balance::BALANCE_SCOPE_UNREG_EXPENSE;
                    $balance
                        ->setDateOccurred(new \DateTime($row->date))
                        ->setDateRecorded(new \DateTime())
                        ->setType($balanceType)
                        ->setValue($row->$type)
                        ->setAccount($em->getReference('PmsFinanceBundle:Account',$row->account))
                        ->setScope($em->getReference('PmsFinanceBundle:Scope', $balanceScope));

                    $em->persist($balance);
                }
            }
        }
    }
}