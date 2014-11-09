<?php

namespace Pms\LayoutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class MenuController extends Controller
{
    public function menuAction($menuSource, $currentRoute = '')
    {
        $menuService = $this->get($menuSource);
        $menu = $menuService->getMenu();


        return $this->render(
            'PmsLayoutBundle:Menu:index.html.twig',
            array(
                'menu' => $menu,
                'currentRoute' => $currentRoute
            )
        );
    }
}