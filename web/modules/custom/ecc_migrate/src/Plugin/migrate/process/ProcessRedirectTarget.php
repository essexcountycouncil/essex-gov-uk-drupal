<?php

namespace Drupal\ecc_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Provides a process_redirect_target plugin.
 *
 * Usage:
 *
 * @code
 * process:
 *   bar:
 *     plugin: process_redirect_target
 *     source: foo
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "process_redirect_target"
 * )
 */
class ProcessRedirectTarget extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Special case for front page.
    if (in_array($value, [
      'https://www.essex.gov.uk/',
      'https://www.essex.gov.uk',
      'https://essex.gov.uk/',
      'https://essex.gov.uk',
    ])) {
      return 'internal:/';
    }

    // Change absolute URLs to relative where possible if for this site.
    if (str_starts_with($value, 'https://www.essex.gov.uk')) {
      return 'internal:' . substr($value, strlen('https://www.essex.gov.uk'));
    }
    if (str_starts_with($value, 'https://essex.gov.uk')) {
      return 'internal:' . substr($value, strlen('https://essex.gov.uk'));
    }

    // Redirects to Contentful assets/images etc. should point at Drupal.
    // This assumes such assets were on ECC Contentful and not somewhere else!
    if (str_starts_with($value, 'https://assets.ctfassets.net/')) {
      return '/sites/default/files/assets.ctfassets.net/' . substr($value, strlen('https://assets.ctfassets.net/'));
    }
    if (str_starts_with($value, 'https://downloads.ctfassets.net/')) {
      return '/sites/default/files/downloads.ctfassets.net/' . substr($value, strlen('https://downloads.ctfassets.net/'));
    }
    if (str_starts_with($value, 'https://images.ctfassets.net/')) {
      return '/sites/default/files/images.ctfassets.net/' . substr($value, strlen('https://images.ctfassets.net/'));
    }
    if (str_starts_with($value, 'https://videos.ctfassets.net/')) {
      return '/sites/default/files/videos.ctfassets.net/' . substr($value, strlen('https://videos.ctfassets.net/'));
    }

    return $value;
  }

}
