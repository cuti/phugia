# PHÚ GIA CCRM

### Install mpdf

1. [Download](https://getcomposer.org/Composer-Setup.exe) and install composer.

1. Uncomment the line **;extension=php_openssl.dll** from _php.ini_ file and restart Apache server.

1. Open command prompt at the root directory of the project (location of _README.md_ file).

1. Execute command
  `composer install --prefer-dist`

1. mpdf library will be installed in _vendor_ directory.