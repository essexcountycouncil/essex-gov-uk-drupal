<?php

namespace Drupal\ecc_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Provides an alert_pages plugin.
 *
 * Usage:
 *
 * @code
 * process:
 *   bar:
 *     plugin: alert_pages
 *     source: foo
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "alert_pages",
 *   handle_multiples = TRUE
 * )
 */
class AlertPages extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!is_array($value)) {
      throw new \InvalidArgumentException();
    }
    $pages = '';
    foreach ($value as $nid) {
      $pages .= "node/$nid\n";
    }
    return [
      'request_path' => [
        'pages' => $pages,
        'negate' => "0",
      ],
    ];
  }

}
