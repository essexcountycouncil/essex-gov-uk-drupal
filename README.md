# Prototype for essex.gov.uk Drupal project

Built using the LocalGov Drupal distribution.

## Quick start for local development

Use [DDEV](https://ddev.readthedocs.io/en/latest/users/install/ddev-installation/).

1. Clone this repository.
2. Run `ddev start` in the project root.
3. Run `ddev composer install` to install the project dependencies.
4. `ddev import-db --src=path/to/20YYMMYY_ecc_gov_dev.sql.gz`
5. `ddev drush deploy`
6. `ddev drush uli`

## PHP requirements
The DDEV `config.yaml` file is configured with the requirements for local development and provides an approximate
equivalent to what is used in production.
- [PHP 8.1](https://www.drupal.org/docs/getting-started/system-requirements/php-requirements)
- [Composer 2.x](https://getcomposer.org/)

## Web server requirements
Once again, DDEV has the details configured for NGINX for local development.
- [See Drupal's web server requirements page](https://www.drupal.org/docs/system-requirements/web-server-requirements)
- [NGINX with drupal](https://www.nginx.com/resources/wiki/start/topics/recipes/drupal/)
