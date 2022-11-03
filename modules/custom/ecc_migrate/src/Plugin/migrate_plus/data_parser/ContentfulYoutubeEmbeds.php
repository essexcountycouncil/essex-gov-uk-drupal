<?php

namespace Drupal\ecc_migrate\Plugin\migrate_plus\data_parser;

use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;

/**
 * Extend the Json data parser for embedded YouTube videos.
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
 *     data_fetcher_plugin: file
 *     data_parser_plugin: contentful_youtube_embeds
 *     item_selector: entries
 * @endcode
 *
 * @DataParser(
 *   id = "contentful_youtube_embeds",
 *   title = @Translation("JSON Contentful")
 * )
 */
class ContentfulYoutubeEmbeds extends Json {
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
