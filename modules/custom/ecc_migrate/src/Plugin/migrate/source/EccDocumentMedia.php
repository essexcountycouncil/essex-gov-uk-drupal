<?php

namespace Drupal\ecc_migrate\Plugin\migrate\source;

use Drupal\Core\Database\Query\SelectInterface;

/**
 * Source plugin for migrating imported files to media documents.
 *
 * @MigrateSource(
 *   id = "ecc_document_media",
 *   source_module = "ecc_migrate"
 * )
 */
class EccDocumentMedia extends EccMedia {

  /**
   * {@inheritdoc}
   */
  public function query(): SelectInterface {
    $query = parent::query();
    $mime_types = [
      'application/CDFV2',
      'application/octet-stream',
      'application/pdf',
      'application/vnd.google-earth.kml+xml',
      'application/vnd.openxmlformats-officedocument.presentationml.presentation',
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      'application/zip',
    ];
    $query->condition('f.filemime', $mime_types, 'IN');
    return $query;
  }

}
