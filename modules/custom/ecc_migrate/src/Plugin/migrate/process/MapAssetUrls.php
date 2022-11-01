<?php

namespace Drupal\ecc_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Wa72\HtmlPageDom\HtmlPageCrawler;

/**
 * Provides a map_asset_urls plugin.
 *
 * This process plugin parses the passed html, and rewrites links that point
 * to the old Contentful site to point at their new migrated location.
 *
 * Usage:
 *
 * @code
 * process:
 *   bar:
 *     plugin: map_asset_urls
 *     source: foo
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "map_asset_urls"
 * )
 */
class MapAssetUrls extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $html_page_crawler = new HtmlPageCrawler($value);

    $html_page_crawler->filter('a')->each(function (HtmlPageCrawler $node) {
      $href = $node->getAttribute('href');
      if ($href) {
        // Strip protocol header.
        if (str_starts_with(strtolower($href), 'https:')) {
          $href = substr($href, strlen('https:'));
        }
        if (str_starts_with(strtolower($href), 'http:')) {
          $href = substr($href, strlen('http:'));
        }
        if (str_starts_with($href, '//assets.ctfassets.net/knkzaf64jx5x')
        || str_starts_with($href, '//downloads.ctfassets.net/knkzaf64jx5x')
        || str_starts_with($href, '//images.ctfassets.net/knkzaf64jx5x')) {
          // This is a migrated asset. Change the link to be relative.
          // First remove the first /.
          $href = substr($href, 1);
          // Then set it within Drupal's public file system.
          $href = '/sites/default/files/migration_data/files' . $href;
          // And save it back to our html.
          $node->setAttribute('href', $href);
        }
      }
    });

    return $html_page_crawler->saveHTML();
  }

}
