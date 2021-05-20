<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
define('ROOT_PATH', dirname(__FILE__));
define('APPLICATION_PATH', realpath(ROOT_PATH . '/application'));
define('APPLICATION_ENV', 'production');
// set_include_path(APPLICATION_PATH . '/../library');

$libPath = realpath(APPLICATION_PATH . '/../library');
$admModelsPath = APPLICATION_PATH . '/modules/admin/models';
$defaultFormsPath = APPLICATION_PATH . '/modules/default/forms';

set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            $libPath,
            $admModelsPath,
            $defaultFormsPath,
            get_include_path()
        )
    )
);

require_once 'Zend/Application.php';

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()->run();
