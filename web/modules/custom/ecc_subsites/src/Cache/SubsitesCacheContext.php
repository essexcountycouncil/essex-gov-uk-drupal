<?php

namespace Drupal\ecc_subsites\Cache;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\Context\CacheContextInterface;
use Drupal\localgov_subsites_extras\Service\SubsiteService;

/**
 * Defines the subsites cache context service.
 *
 * Cache context ID: 'subsites'.
 */
class SubsitesCacheContext implements CacheContextInterface {

  /**
   * Constructor.
   *
   * @param \Drupal\localgov_subsites_extras\Service\SubsiteService $subsiteService
   *   The localgov_subsites_extras.service service.
   */
  public function __construct(protected SubsiteService $subsiteService) {
  }

  /**
   * {@inheritdoc}
   */
  public static function getLabel() {
    return t("Subsites");
  }

  /**
   * {@inheritdoc}
   */
  public function getContext(): string {
    return $this->subsiteService->getCurrentSubsiteTheme() ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheableMetadata(): CacheableMetadata {
    return new CacheableMetadata();
  }

}
