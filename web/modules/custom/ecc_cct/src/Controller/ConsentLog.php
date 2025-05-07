<?php

declare(strict_types=1);

namespace Drupal\ecc_cct\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Returns responses for ECC Cookie Consent Tracker routes.
 */
final class ConsentLog extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('database'));
  }

  /**
   * Constructs a database connection service object.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The connection.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

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
    // If this isn't turned on, bail.
    if (!$this->config('ecc_cct.settings')->get('enable')) {
      throw new ServiceUnavailableHttpException(NULL, 'Service unavailable');
    }
    // Otherwise, let's write some data.
    $content = [
      'status' => 'success',
      'message' => "Received ID: $choice",
    ];

    $this->connection->insert('ecc_cct_data')
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
