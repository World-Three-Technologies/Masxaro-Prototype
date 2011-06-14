###phpunit install:

1. install pear:

    $ wget http://pear.php.net/go-pear.phar$ php -d detect_unicode=0 go-pear.phar

2. install phpunit

    pear channel-discover pear.phpunit.de
    pear channel-discover components.ez.no
    pear channel-discover pear.symfony-project.com
    pear install phpunit/PHPUnit

3.set pear include path:

    $ pear config-get php_dir> /usr/share/lib/php/ (or local path like /Users/Jimmy/pear/share/pear)

check where the php.ini file is

    $ php --ini

open php.ini and add include path with the path of pear php_dir:

in php.ini:

    include_path = "... : /path/to/pear/lib"
