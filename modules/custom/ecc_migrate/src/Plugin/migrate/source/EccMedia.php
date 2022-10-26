<?php

namespace Drupal\ecc_migrate\Plugin\migrate\source;

use Drupal\Core\Database\Query\SelectInterface;
use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Base source class for creating media items from migrated files.
 */
abstract class EccMedia extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query(): SelectInterface {
    $query = $this->select('migrate_map_ecc_files', 'm');
    $query->addJoin('LEFT OUTER', 'file_managed', 'f', 'm.destid1 = f.fid');
    $query->fields('m', ['destid1']);
    $query->fields('f', ['fid', 'filename', 'uid']);
    $query->condition('m.destid1', NULL, 'IS NOT NULL');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields(): array {
    return [
      'fid' => $this->t('The file ID.'),
      'filename' => $this->t('The name of the file'),
      'uid' => $this->t('The id of the user creating the file'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds(): array {
    $ids['fid'] = [
      'type' => 'integer',
      'unsigned' => TRUE,
      'size' => 'big',
    ];
    return $ids;
  }

}
