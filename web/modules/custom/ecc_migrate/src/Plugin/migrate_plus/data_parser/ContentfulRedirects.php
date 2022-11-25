<?php

namespace Drupal\ecc_migrate\Plugin\migrate_plus\data_parser;

/**
 * Extend the Contentful Data Parser to handle redirects.
 *
 * Contentful includes two types of redirect, redirects and legacyUrls.
 * These are specified in the redirect_type parameter of the configuration.
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
 *     redirect_type: legacyUrls
 *
 * @endcode
 *
 * @DataParser(
 *   id = "contentful_redirects",
 *   title = @Translation("JSON Contentful")
 * )
 */
class ContentfulRedirects extends JsonContentful {

  /**
   * Redirect type for this source.
   */
  protected string $redirectType;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    // We can default the content_type - no need to have it in the yaml.
    $configuration['content_type'] = 'redirect';
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->redirectType = $configuration['redirect_type'];
  }

  /**
   * {@inheritdoc}
   */
  protected function getSourceData(string $url): array {
    // The parent class will only return a single row.
    // Each redirect is held in an array in a field in that row.
    // The individual array items are what we want to return as source data.
    $redirect_data = parent::getSourceData($url);

    $source_data = [];

    // We can't guarantee there won't be multiple rows by the time the real
    // migration is run, so we still iterate over the set.
    foreach ($redirect_data as $redirect_datum) {
      foreach ($redirect_datum['fields'][$this->redirectType]['en-GB'] as $target => $location) {
        // Ignore the wildcard redirects. Unlike Contentful, Drupal does not
        // support them for performance reasons. These will be moved to server
        // configuration instead.
        if (!str_ends_with($target, '*')) {
          $source_data[] = [
            'target' => $target,
            'location' => $location,
          ];
        }
      }
    }

    return $source_data;
  }

}
