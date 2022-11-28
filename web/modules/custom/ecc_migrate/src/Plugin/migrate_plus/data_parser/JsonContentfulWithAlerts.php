<?php

namespace Drupal\ecc_migrate\Plugin\migrate_plus\data_parser;

/**
 * Extend the Contentful JSON data parser to include alert information.
 *
 * Usage:
 *
 * @code
 *   source:
 *     plugin: url
 *     urls:
 *       - 'private://path/to/export.json'
 *     data_fetcher_plugin: file
 *     data_parser_plugin: json_contentful_with_alerts
 *     item_selector: entries
 *     content_type: news
 *
 * @endcode
 *
 * @DataParser(
 *   id = "json_contentful_with_alerts",
 *   title = @Translation("JSON Contentful with Alerts")
 * )
 */
class JsonContentfulWithAlerts extends JsonContentful {

}
