<?php

namespace Drupal\ecc_migrate\Plugin\migrate_plus\data_parser;

use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;

/**
 * Extend the Json data parser for Contentful Content Types.
 *
 * The selectors provided by the migrate_plus JSON data parser don't allow us
 * to restrict content by type. This could just be done at migrate time but
 * then the file counts would be inaccurate.
 *
 * This subclass allows a Contentful content type to be specified and all
 * non-matching content will be filtered out at the source.
 *
 * Usage:
 *
 * @code
 *   source:
 *     plugin: url
 *     urls:
 *       - 'private://path/to/export.json'
 *     data_fetcher_plugin: file
 *     data_parser_plugin: json_contentful
 *     item_selector: entries
 *     content_type: news
 *
 * @endcode
 *
 * @DataParser(
 *   id = "json_contentful",
 *   title = @Translation("JSON Contentful")
 * )
 */
class JsonContentful extends Json {

  /**
   * Content type for this source in the JSON export from Contentful.
   *
   * @var string
   */
  protected string $contentType;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->contentType = $configuration['content_type'] ?? '';
  }

  /**
   * {@inheritdoc}
   */
  protected function getSourceData(string $url): array {
    return array_filter(parent::getSourceData($url), function ($value) {
      if (!isset($value['sys']['contentType']['sys']['id'])) {
        return FALSE;
      }
      if ($value['sys']['contentType']['sys']['id'] != $this->contentType) {
        return FALSE;
      }
      return TRUE;

    });
  }

}
