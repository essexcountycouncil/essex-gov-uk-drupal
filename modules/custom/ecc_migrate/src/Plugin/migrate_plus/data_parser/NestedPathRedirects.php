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

    // We need to create redirects where the source url is in the format
    // <article slug>/<section slug>.
    $articles = array_filter($source_data, function ($datum) {
      if (!isset($datum['sys']['contentType']['sys']['id'])) {
        return FALSE;
      }
      if ($datum['sys']['contentType']['sys']['id'] != 'article') {
        return FALSE;
      }
      if (!isset($datum['fields']['sections'])) {
        return FALSE;
      }
      return TRUE;
    });

    $sections = array_filter($source_data, function ($datum) {
      if (!isset($datum['sys']['contentType']['sys']['id'])) {
        return FALSE;
      }
      if ($datum['sys']['contentType']['sys']['id'] != 'section') {
        return FALSE;
      }
      return TRUE;
    });

    // Create a map of section IDs to their slugs.
    $map_section_id_to_slug = [];
    foreach ($sections as $section) {
      if (isset($section['fields']['slug']['en-GB']) && isset($section['sys']['id'])) {
        $map_section_id_to_slug[$section['sys']['id']] = $section['fields']['slug']['en-GB'];
      }
    }

    // Go through all the articles. For each article's sections, work out their
    // path based on the article slug and the section slug.
    $source_data = [];
    foreach ($articles as $article) {
      foreach ($article['fields']['sections']['en-GB'] as $section) {
        if (!isset($map_section_id_to_slug[$section['sys']['id']])) {
          // We don't know the slug for this section so ignore.
          continue;
        }
        // Note we set two IDs in the source. section_id is the ID of the
        // section; we need this to look up the node ID, but we can't use it
        // to identify the source row in the migration in case sections are
        // included in multiple guide pages/articles.
        $source_data[] = [
          'full_path' => $article['fields']['slug']['en-GB'] . '/' . $map_section_id_to_slug[$section['sys']['id']],
          'section_id' => $section['sys']['id'],
          'migrate_source_id' => $article['sys']['id'] . ':' . $section['sys']['id'],
        ];
      }
    }

    return $source_data;
  }

}
