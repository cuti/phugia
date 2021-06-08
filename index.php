<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
define('ROOT_PATH', dirname(__FILE__));
define('APPLICATION_PATH', realpath(ROOT_PATH . '/application'));
define('APPLICATION_ENV', 'production');
define('ADMIN_EMAIL', 'huylm534@gmail.com');
define('ADMIN_EMAIL_SECRET', 'aHV5bG1AMTIz');
define('MIN_PASS_LEN', 8);
define('SMTP_SERVER', 'smtp.gmail.com');
define('SMTP_SSL', 'tls');
define('SMTP_PORT', 587);

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
