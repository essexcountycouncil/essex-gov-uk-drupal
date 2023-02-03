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
 * Additional alert information will be included in the source if with_alerts
 * is TRUE. By default, alert information is not included.
 *
 * If tags are specified, the source will be restricted to content matching
 * one of the specified tag IDs.
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
 *     with_alerts: true
 *     tags:
 *       - normalAlertBanner
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
   * Whether to include alert information in the source.
   *
   * @var bool
   */
  protected bool $withAlerts;

  /**
   * Optional list of tags to which to restrict content.
   */
  protected array $tags;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->contentType = $configuration['content_type'] ?? '';
    $this->withAlerts = $configuration['with_alerts'] ?? FALSE;
    $this->tags = $configuration['tags'] ?? [];
  }

  /**
   * {@inheritdoc}
   */
  protected function getSourceData(string $url): array {
    // If we need alerts, get them from the source and build them into a map
    // keyed by ID, for easier cross-referencing against each source item.
    $alerts = [];
    $map_id_to_alerts = [];
    if ($this->withAlerts) {
      $alerts = array_filter(parent::getSourceData($url), function ($value) {
        if (!isset($value['sys']['contentType']['sys']['id'])) {
          return FALSE;
        }
        if ($value['sys']['contentType']['sys']['id'] != 'alert') {
          return FALSE;
        }
        return TRUE;
      });
      foreach ($alerts as $alert) {
        if (!isset($alert['sys']['id'])) {
          continue;
        }
        $map_id_to_alerts[$alert['sys']['id']] = $alert;
      }
    }

    // Filter our source data to the content type in which we are interested.
    $source_data = array_filter(parent::getSourceData($url), function ($value) {
      if (!isset($value['sys']['contentType']['sys']['id'])) {
        return FALSE;
      }
      if ($value['sys']['contentType']['sys']['id'] != $this->contentType) {
        return FALSE;
      }
      // If we are filtering by tag..
      if (!empty($this->tags)) {
        // ..but the source row has no tags..
        if (!isset($value['metadata']['tags'])) {
          // ..remove the row from the data set..
          return FALSE;
        }
        // ..but if we have tags, iterate through to see if this row has a
        // matching tag.
        $tag_match = FALSE;
        foreach ($value['metadata']['tags'] as $source_tag) {
          if (in_array($source_tag['sys']['id'], $this->tags)) {
            $tag_match = TRUE;
          }
        }
        // We didn't find a matching tag, so exclude.
        if (!$tag_match) {
          return FALSE;
        }
      }
      return TRUE;
    });

    // If we're not concerned with alerts..
    if ($this->withAlerts === FALSE) {
      // ..that's all we need to do.
      return $source_data;
    }

    // Otherwise, go through our source data and add the relevant alert(s)
    // information to each element.
    foreach ($source_data as &$datum) {
      $datum['alerts'] = [];
      if (!isset($datum['fields']['alertsInline']['en-GB'])) {
        continue;
      }
      foreach ($datum['fields']['alertsInline']['en-GB'] as $alert) {
        if (array_key_exists($alert['sys']['id'], $map_id_to_alerts)) {
          // Note that alerts are keyed by title rather than ID, as that is
          // how they are referred in the body text.
          $datum['alerts'][$map_id_to_alerts[$alert['sys']['id']]['fields']['title']['en-GB']] = $map_id_to_alerts[$alert['sys']['id']];
        }
      }
    }

    return $source_data;
  }

}
