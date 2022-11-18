<?php

namespace Drupal\ecc_migrate\Plugin\migrate_plus\data_parser;

use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;

/**
 * JSON parser for building nested redirects.
 *
 * Normally we use post-migrate events for calculating a redirect.
 * However, for nested content, each row does not know its own full source
 * path. Work needs to be done in a data parser plugin to build full source
 * paths so the redirects can be migrated.
 *
 * @DataParser(
 *   id = "nested_path_redirects",
 *   title = @Translation("Nested Path Redirects")
 * )
 */
class NestedPathRedirects extends Json {

  const CONTENTFUL_HOMEPAGE_ID = '4I5peHWWwUWOyoyCAcs4C2';

  /**
   * {@inheritdoc}
   */
  protected function getSourceData(string $url): array {
    $source_data = parent::getSourceData($url);

    // Filter out the content types we are not migrating, or that do not use
    // nested paths.
    $source_data = array_filter($source_data, function ($datum) {
      $nested_contentful_content_types = [
        'topic',
        'section',
        'article',
      ];
      if (!isset($datum['sys']['contentType']['sys']['id'])) {
        return FALSE;
      }
      if (in_array($datum['sys']['contentType']['sys']['id'], $nested_contentful_content_types)) {
        return TRUE;
      }
      return FALSE;

    });

    // We now have the JSON source data for all our content types.
    // We need to go through that to build a map of paths.
    $id_slug_map = [];
    $id_parent_map = [];
    $id_type_map = [];

    foreach ($source_data as $source_datum) {
      $id_slug_map[$source_datum['sys']['id']] = $source_datum['fields']['slug']['en-GB'];
      $id_parent_map[$source_datum['sys']['id']] = $source_datum['fields']['parentPage']['en-GB']['sys']['id'] ?? NULL;
      $id_type_map[$source_datum['sys']['id']] = $source_datum['sys']['contentType']['sys']['id'] ?? NULL;
      // If the parent is the homepage, treat it as NULL.
      if ($id_parent_map[$source_datum['sys']['id']] === NestedPathRedirects::CONTENTFUL_HOMEPAGE_ID) {
        $id_parent_map[$source_datum['sys']['id']] = NULL;
      }
    }

    $id_full_path_map = [];
    foreach ($id_parent_map as $id => $parent_id) {
      $id_full_path_map[$id] = $id_slug_map[$id];
      $ultimate_parent_is_topic = $id_type_map[$id] === 'topic';
      while ($parent_id) {
        $id_full_path_map[$id] = $id_slug_map[$parent_id] . '/' . $id_full_path_map[$id];
        $ultimate_parent_is_topic = $id_type_map[$parent_id] === 'topic';
        $parent_id = $id_parent_map[$parent_id];
      }
      // If the final parent in the chain is a topic..
      if ($ultimate_parent_is_topic) {
        // ..prefix the path appropriately.
        $id_full_path_map[$id] = 'topic/' . $id_full_path_map[$id];
      }
    }

    // Add the full path back to the source data so it is available to the
    // migration.
    foreach ($source_data as $source_datum) {
      $source_datum['full_path'] = $id_full_path_map[$source_datum['sys']['id']];
    }

    return $source_data;
  }

}
