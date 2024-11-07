<?php

declare(strict_types=1);

namespace Drupal\ecc_cct\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns responses for ECC Cookie Consent Tracker routes.
 */
final class ConsentLog extends ControllerBase {

  /**
   * Handles requests to /ecc-cct/js/log/{$choice}.
   *
   * @param int $choice
   *   The dynamic ID passed to the route.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The HTTP request object.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   A response object.
   */
  public function log(int $choice, Request $request): Response {
    $content = [
      'status' => 'success',
      'message' => "Received ID: $choice",
    ];

    /** @var \Drupal\Core\Database\Connection $connection */
    $connection = \Drupal::service('database');
    $result = $connection->insert('ecc_cct_data')
      ->fields([
        'choice' => $choice,
        'timestamp' => time(),
      ])
      ->execute();

    // Maybe long term, we don't care about a response...
    // but just in case, for testing, let's leave it in.
    return new Response(json_encode($content), 200, ['Content-Type' => 'application/json']);
  }

}
