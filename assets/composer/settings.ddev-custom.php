<?php

/**
 * @file
 * Drupal settings file for DDEV.
 */

$settings['container_yamls'][] = $app_root . '/' . $site_path . '/../development.services.yml';
$config['config_split.config_split.ddev']['status'] = TRUE;
$config['config_split.config_split.azure']['status'] = FALSE;

