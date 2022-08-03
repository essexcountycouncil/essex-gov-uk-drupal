# Prototype for essex.gov.uk Drupal project

Built using the LocalGov Drupal distribution.

## PHP requirements
https://www.drupal.org/docs/system-requirements/php-requirements
PHP 8.1

### Extensions
See ext-* in composer.json
https://git.drupalcode.org/project/drupal/blob/9.1.x/core/composer.json

Used by Ramblers
* php7.4-fpm
* php7.4-xml
* php7.4-mbstring
* php7.4-mysqli
* php7.4-pgsql
* php7.4-gd
* php7.4-curl
* php7.4-zip
* php7.4-imagick

### Other
Composer

## Web server requirements
* https://www.drupal.org/docs/system-requirements/web-server-requirements
* https://www.nginx.com/resources/wiki/start/topics/recipes/drupal/

## Deployment steps
From the root of this repository:
* composer install
* composer update-drupal
