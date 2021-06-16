<?php

class MenuController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->BaseUrl = $this->getRequest()->getBaseUrl();
    }

    public function indexAction()
    {
        $module = $this->getRequest()->getParam('module');
        $username = Zend_Auth::getInstance()->getIdentity()['username'];

        $menus = new Default_Model_Menu();
        $data = $menus->getMenuByUsername($username, $module);

        $rootUrl = $this->view->BaseUrl;
        $s = '';

        foreach ($data as $menu) {
            $s .= '<li class="nav-item">';

            if ($menu['menu_url'] != null) {
                $s .= '<a class="nav-link" href="' . $rootUrl . '/' . $menu['menu_url'] . '" title="' . $menu['menu_name'] . '">';
                $s .= '<i class="fa-fw ' . $menu['menu_icon'] . '"></i>';
                $s .= '<span>' . $menu['menu_name'] . '</span></a></li>';
            } else {
                $submenus = $menus->getSubMenuByUsernameAndParentId($username, $menu['menu_id'], $module);

                $s .= '<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse' . $menu['menu_group'] . '" aria-expanded="false" aria-controls="collapse' . $menu['menu_group'] . '">';
                $s .= '<i class="fa-fw ' . $menu['menu_icon'] . '"></i>';
                $s .= '<span>' . $menu['menu_name'] . '</span></a>';
                $s .= '<div id="collapse' . $menu['menu_group'] . '" class="collapse" aria-labelledby="heading' . $menu['menu_group'] . '" data-parent="#accordionSidebar">';
                $s .= '<div class="sub-wrapper py-2 collapse-inner rounded">';

                foreach ($submenus as $submenu) {
                    $s .= '<a class="collapse-item" href="' . $rootUrl . '/' . $submenu['menu_url'] . '">';
                    $s .= '<i class="fa-fw ' . $submenu['menu_icon'] . ' mr-2"></i>';
                    $s .= $submenu['menu_name'] . '</a>';
                }

                $s .= '</div></div></li>';

                unset($submenus);
            }
        }

        if ($s !== '') {
            $s = '<hr class="sidebar-divider my-0">' . $s;
        }

        $this->view->menuHtml = $s;
    }
}
