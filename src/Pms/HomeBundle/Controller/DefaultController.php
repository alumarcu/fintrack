<?php

namespace Pms\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('PmsHomeBundle:Default:index.html.twig', array());
    }
}
