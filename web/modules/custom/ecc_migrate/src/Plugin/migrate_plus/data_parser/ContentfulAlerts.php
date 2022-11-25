<?php

namespace Drupal\ecc_migrate\Plugin\migrate_plus\data_parser;

use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;

/**
 * Extension of Json data parser plugin for retrieving alerts.
 *
 * Usage:
 *
 * @code
 *   source:
 *     plugin: url
 *     urls:
 *       - 'private://path/to/export.json'
 *     data_fetcher_plugin: file
 *     data_parser_plugin: contentful_alerts
 *     item_selector: entries
 * @endcode
 *
 * @DataParser(
 *   id = "contentful_alerts",
 *   title = @Translation("Contentful Alerts")
 * )

 */
class ContentfulAlerts extends Json {

  /**
   * {@inheritdoc}
   */
  protected function getSourceData(string $url): array {
    // In Drupal, the visibility of whether an alert is displayed on a page
    // is configured against the alert.
    // In Contentful, it is configured against the pages.
    // Therefore, to migrate the alerts, we need to consider all the content
    // types we are migrating alongside the alerts themselves, so we can
    // collate and add the relevant visibility data to the alert source rows.
    $source_data = parent::getSourceData($url);

    // First split the source data, which will be all content type under
    // 'entries' into one set of alerts, and one set of pages that might
    // contain alerts.
    $alerts = array_filter($source_data, function ($source_row) {
      if (!isset($source_row['sys']['contentType']['sys']['id'])) {
        return FALSE;
      }
      if ($source_row['sys']['contentType']['sys']['id'] == 'alert') {
        return TRUE;
      }
      return FALSE;
    });

    $pages = array_filter($source_data, function ($source_row) {
      if (!isset($source_row['sys']['contentType']['sys']['id'])) {
        return FALSE;
      }
      return (in_array($source_row['sys']['contentType']['sys']['id'], [
        'topic',
        'news',
        'article',
      ]));
    });

    // Now iterate through all the pages, to build a map of alert IDs to the
    // set of pages that contain that alert ID.
    $map_alert_id_to_pages_ids = [];
    foreach ($pages as $page) {
      if (isset($page['fields']['alerts']['en-GB'])) {
        foreach ($page['fields']['alerts']['en-GB'] as $alert) {
          if (array_key_exists($alert['sys']['id'], $map_alert_id_to_pages_ids)) {
            $map_alert_id_to_pages_ids[$alert['sys']['id']] = [$page['sys']['id']];
          }
          else {
            $map_alert_id_to_pages_ids[$alert['sys']['id']][] = $page['sys']['id'];
          }
        }
      }
    }

    // Finally, go through our set of alerts, adding an alert_pages array for
    // each, and populating it with the IDs of the pages containing that alert,
    // if any.
    foreach ($alerts as &$alert) {
      $alert['alert_pages'] = [];
      if (array_key_exists($alert['sys']['id'], $map_alert_id_to_pages_ids)) {
        $alert['alert_pages'] = $map_alert_id_to_pages_ids[$alert['sys']['id']];
      }
    }

    return $alerts;
  }

}
