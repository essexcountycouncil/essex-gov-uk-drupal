<?php

namespace Drupal\ecc_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * BETTER NAME HERE.
 *
 * Usage:
 *
 * @code
 * process:
 *   bar:
 *     plugin: extract_contact_details
 *     source: foo
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "extract_contact_details"
 * )
 */
class ExtractContactDetails extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!isset($value['metadata']['tags'][0]['sys']['id'])) {
      return '';
    }
    if ($value['metadata']['tags'][0]['sys']['id'] != 'contactDetails') {
      return '';
    }
    return $value['sys']['id'];
  }

}
