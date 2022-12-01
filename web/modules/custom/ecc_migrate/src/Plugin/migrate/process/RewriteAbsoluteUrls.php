<?php

namespace Drupal\ecc_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Wa72\HtmlPageDom\HtmlPageCrawler;

/**
 * Rewrites absolute links in HTML to be relative.
 *
 * Usage:
 *
 * @code
 * process:
 *   bar:
 *     plugin: rewrite_absolute_urls
 *     source: foo
 *     urls:
 *       - 'https://example.com'
 *       - 'http://example.com'
 *       - 'https://www.example.com'
 *       - 'http://www.example.com'
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "rewrite_absolute_urls"
 * )
 */
class RewriteAbsoluteUrls extends ProcessPluginBase {

  /**
   * Base urls we should remap.
   */
  protected array $urls;

  /**
   * Constructs a RewriteAbsoluteUrls plugin.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->urls = $configuration['urls'] ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $crawler = new HtmlPageCrawler($value);
    $crawler->filter('a[href]')->each(function (HtmlPageCrawler $a) {
      $href = $a->getAttribute('href');
      foreach ($this->urls as $base_url) {
        if (str_starts_with(strtolower($href), $base_url)) {
          $href = substr($href, strlen($base_url));
          if ($href == '') {
            $href = '/';
          }
          $a->setAttribute('href', $href);
        }
      }
    });

    return $crawler->saveHTML();
  }

}
