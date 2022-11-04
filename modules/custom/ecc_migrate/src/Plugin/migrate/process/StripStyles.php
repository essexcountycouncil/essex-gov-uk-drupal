<?php

namespace Drupal\ecc_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Strips style information from HTML.
 *
 * This gets included in the export when YouTube videos have been embedded.
 *
 * Usage:
 *
 * @code
 * process:
 *   bar:
 *     plugin: strip_styles
 *     source: foo
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "strip_styles"
 * )
 */
class StripStyles extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $style_snippet = "<style>.embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; } .embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }</style>";
    return str_replace($style_snippet, '', $value);
  }

}
