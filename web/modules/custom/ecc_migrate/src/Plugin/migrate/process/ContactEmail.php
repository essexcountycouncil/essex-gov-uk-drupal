<?php

namespace Drupal\ecc_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Gets email address from contact body.
 *
 * Usage:
 *
 * @code
 * process:
 *   bar:
 *     plugin: contact_email
 *     source: foo
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "contact_email"
 * )
 */
class ContactEmail extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $value = explode("\n", $value);
    foreach ($value as $line) {
      if (str_starts_with($line, 'Email: ')) {
        if (str_contains($line, '<')) {
          $start = strpos($line, '<') + 1;
          $end = strpos($line, '>') - $start;
          return substr($line, $start, $end);
        }
        if (str_contains($line, '[')) {
          $start = strpos($line, '[') + 1;
          $end = strpos($line, ']') - $start;
          return substr($line, $start, $end);
        }
      }
    }
    // Did not find an email address.
    return '';
  }

}
