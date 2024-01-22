<?php

/**
 * @file
 * Docksal no-cache development override configuration feature.
 */

// Docksal DB connection settings.
$databases['default']['default'] = [
  'database' => 'default',
  'username' => 'user',
  'password' => 'user',
  'host' => 'db',
  'driver' => 'mysql',
];

$settings['hash_salt'] = 'changeme';

// Be picky about error reporting.
$config['system.logging']['error_level'] = 'verbose';
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

// Disable CSS and JS aggregation.
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;

// Override default port configuration for Solr.
$config['search_api.server.solr']['backend_config']['connector_config']['scheme'] = 'http';
$config['search_api.server.solr']['backend_config']['connector_config']['host'] = 'solr';
$config['search_api.server.solr']['backend_config']['connector_config']['port'] = '8983';
$config['search_api.server.solr']['backend_config']['connector_config']['path'] = '/';
$config['search_api.server.solr']['backend_config']['connector_config']['core'] = 'user-owned';

// Skip file system permissions hardening.
$settings['skip_permissions_hardening'] = TRUE;

// Set the trusted host variable (https://www.drupal.org/node/1992030)
$settings['trusted_host_patterns'] = [
  'localhost',
  $_SERVER['VIRTUAL_HOST'],
  '${PROJECT_NAME}',
];

// Stage File Proxy settings.
$config['stage_file_proxy.settings']['origin'] = '${STAGE_FILE_PROXY_URL}';

// Private file settings
$settings['file_private_path'] = '../private';

// Enable local development services.
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/default/development.services.yml';

// Disable the render cache (this includes the page cache).
$settings['cache']['bins']['render'] = 'cache.backend.null';

// Disable Dynamic Page Cache.
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';

// Enable access to rebuild.PHP.
$settings['rebuild_access'] = TRUE;

// Ensure swiftmailer sends to Mailhog.
// see https://blog.docksal.io/mailhog-and-swiftmailer-in-local-development-102ce0c2a631
$config['swiftmailer.transport']['transport'] = 'smtp';
$config['swiftmailer.transport']['smtp_host'] = 'mail';
$config['swiftmailer.transport']['smtp_port'] = '1025';
$config['swiftmailer.transport']['smtp_encryption'] = 0;
$config['swiftmailer.transport']['smtp_credential_provider'] = 'swiftmailer';

// Exclude modules from config export (https://www.drupal.org/node/3079028)
$settings['config_exclude_modules'] = [
  'devel',
  'devel_a11y',
  'stage_file_proxy',
  'twig_vardumper',
];

// Include Docksal project-specific configuration file
if (file_exists($app_root . '/' . $site_path . '/settings.project.php')) {
  include $app_root . '/' . $site_path . '/settings.project.php';
}
