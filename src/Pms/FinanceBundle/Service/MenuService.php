<?php
namespace Pms\FinanceBundle\Service;

class MenuService
{
    public function __construct()
    {
    }

    /**
     * @return array
     */
    public function getMenu()
    {
        $menu = array(
            array(
                'name' => 'Dashboard',
                'location' => 'pms_finance_dashboard',
                'icon' => 'fa-dashboard'
            ),
            array(
                'name' => 'Data Importer',
                'location' => 'pms_finance_importer',
                'icon' => 'fa-android'
            )
        );
        return $menu;
    }
}
