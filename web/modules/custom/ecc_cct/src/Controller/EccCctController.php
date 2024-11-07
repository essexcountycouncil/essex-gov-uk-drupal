<?php

declare(strict_types=1);

namespace Drupal\ecc_cct\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for ECC Cookie Consent Tracker routes.
 */
final class EccCctController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build(): array {
    $sql = "SELECT (COUNT(CASE WHEN choice = 1 THEN 1 END) / COUNT(*)) * 100 AS optin FROM {ecc_cct_data};";
    $database = \Drupal::database();
    $query = $database->query($sql);
    $result = $query->fetchAll();

    $stat = $result[0]->optin;
    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t($stat . '% of people opted into cookies.'),
    ];

    return $build;
  }

}
