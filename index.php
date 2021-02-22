<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
define('APPLICATION_PATH',
    realpath(dirname(__FILE__) . '/application'));
define('APPLICATION_ENV', 'production');
// set_include_path(APPLICATION_PATH . '/../library');

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    APPLICATION_PATH . '/modules/admin/models', APPLICATION_PATH . '/modules/default/forms',
    get_include_path(),
)));

require_once 'Zend/Application.php';

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()->run();
