<?php

namespace Drupal\ecc_migrate\Plugin\migrate_plus\data_parser;

use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;

/**
 * Extend the Json data parser for Contentful Files.
 *
 * We can actually use the default Json parser to source all the files.
 * However, there is a requirement to filter the files to those actually used
 * in content. This source plugin cross references the files from contentful
 * to the export of a web spider of the legacy site.
 *
 * @DataParser(
 *   id = "contentful_files",
 *   title = @Translation("Contentful Files")
 * )
 */
class ContentfulFiles extends Json {

  /**
   * {@inheritdoc}
   */
  protected function getSourceData(string $url): array {
    return array_filter(parent::getSourceData($url), function ($value) {
      // Ignore files with an empty URL. We can't migrate them.
      if (empty($value['fields']['file']['en-GB']['url'])) {
        return FALSE;
      }
      return TRUE;
    });
  }

}
