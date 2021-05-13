<?php

class HeaderController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->BaseUrl = $this->_request->getBaseUrl();
    }

    public function indexAction()
    {
        $this->auth = Zend_Auth::getInstance();
        $this->identity = $this->auth->getIdentity();

        $username = $this->identity->user_username;
        $menus = new Default_Model_Menu();
        $data = $menus->getMenuByUsername($username);

        $rootUrl = $this->_request->getBaseUrl();
        $s = '';

        foreach ($data as $menu) {
            $s .= '<li><a href="';

            if ($menu['menu_url'] != null) {
                $s .= $rootUrl . '/' . $menu['menu_url'] . '" title="' . $menu['menu_name'];
                $s .= '"><i class="icon icon-th-list"></i><span>' . $menu['menu_name'] . '</span></a>';
            } else {
                $submenus = $menus->getSubMenuByUsernameAndParentId($username, $menu['menu_id']);

                $s .= '#" title="' . $menu['menu_name'] . '"><i class="icon icon-th-list"></i><span>';
                $s .= $menu['menu_name'] . '</span></a><ul>';

                foreach ($submenus as $submenu) {
                    $s .= '<li><a class="' . count($submenus) . '" href="' . $rootUrl . '/' . $submenu['menu_url'] . '">';
                    $s .= '<span>' . $submenu['menu_name'] . '</span></a></li>';
                }

                $s .= '</ul>';

                unset($submenus);
            }

            $s .= '</li>';
        }

        $this->view->menu = $data;
        $this->view->menuHtml = $s;
    }

    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        $this->_redirect('/login');
    }
}
