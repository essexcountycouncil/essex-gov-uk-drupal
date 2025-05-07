<?php

declare(strict_types=1);

namespace Drupal\ecc_cct\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for ECC Cookie Consent Tracker routes.
 */
final class EccCctController extends ControllerBase {

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
   * Builds the response.
   */
  public function build(): array {
    $sql = "SELECT (COUNT(CASE WHEN choice = 1 THEN 1 END) / COUNT(*)) * 100 AS optin FROM {ecc_cct_data};";
    $query = $this->connection->query($sql);
    $result = $query->fetchAll();
    $stat = $result[0]->optin;
    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('@stat% of people opted into cookies.', ['@stat' => $stat]),
    ];

    return $build;
  }

}
