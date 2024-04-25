<?php

namespace Drupal\ecc_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Provides a process_redirect_source plugin.
 *
 * Usage:
 *
 * @code
 * process:
 *   bar:
 *     plugin: process_redirect_source
 *     source: foo
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "process_redirect_source"
 * )
 */
class ProcessRedirectSource extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (str_starts_with($value, 'https://www.essex.gov.uk/')) {
      return substr($value, strlen('https://www.essex.gov.uk/'));
    }
    if (str_starts_with($value, 'https://essex.gov.uk/')) {
      return substr($value, strlen('https://essex.gov.uk/'));
    }
    if (str_starts_with($value, '/')) {
      return substr($value, 1);
    }
    return $value;
  }

}
