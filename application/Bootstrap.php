<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initRequest()
    {
        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        $request = new Zend_Controller_Request_Http();
        $front->setRequest($request);
    }

    protected function _initAutoLoad()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(
            new Zend_Controller_Plugin_ErrorHandler(
                array(
                    'module' => 'admin',
                    'controller' => 'error',
                    'action' => 'error',
                )
            )
        );

        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->suppressNotFoundWarnings(false);

        $moduleLoader = new Zend_Application_Module_Autoloader(
            array(
                'namespace' => '',
                'basePath' => APPLICATION_PATH,
            ),
            array(
                'namespace' => '',
                'basePath' => APPLICATION_PATH . ''
            )
        );
        return $moduleLoader;
    }
}
