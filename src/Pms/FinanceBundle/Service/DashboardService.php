<?php
namespace Pms\FinanceBundle\Service;

use Doctrine\ORM\EntityManager;
use Pms\FinanceBundle\Entity\Transaction;
use Pms\FinanceBundle\Form\Type\BalanceType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

class DashboardService
{
    protected $em;
    protected $formFactory;

    public function __construct(EntityManager $em, FormFactory $formFactory)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
    }


}