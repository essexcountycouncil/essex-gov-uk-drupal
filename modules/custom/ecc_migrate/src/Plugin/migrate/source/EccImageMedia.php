<?php

namespace Drupal\ecc_migrate\Plugin\migrate\source;

use Drupal\Core\Database\Query\SelectInterface;

/**
 * Source plugin for migrating imported files to media images.
 *
 * @MigrateSource(
 *   id = "ecc_image_media",
 *   source_module = "ecc_migrate"
 * )
 */
class EccImageMedia extends EccMedia {

  /**
   * {@inheritdoc}
   */
  public function query(): SelectInterface {
    $query = parent::query();
    $mime_types = [
      'image/gif',
      'image/jpeg',
      'image/png',
    ];
    $query->condition('f.filemime', $mime_types, 'IN');
    return $query;
  }

}
