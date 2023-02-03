<?php

namespace Drupal\ecc_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Gets phone number from contact body.
 *
 * Usage:
 *
 * @code
 * process:
 *   bar:
 *     plugin: contact_phone
 *     source: foo
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "contact_phone"
 * )
 */
class ContactPhone extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $value = explode("\n", $value);
    foreach ($value as $line) {
      if (str_starts_with($line, 'Telephone: ')) {
        return substr($line, strlen('Telephone: '));
      }
    }
    // Did not find a telephone number.
    return '';
  }

}
