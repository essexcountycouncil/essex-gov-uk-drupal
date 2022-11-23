<?php

namespace Drupal\ecc_migrate\Plugin\migrate_plus\data_parser;

use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;

/**
 * Extend the Json data parser for Contentful Files.
 *
 * We can actually use the default Json parser to source all the files.
 * However, there is a requirement to filter the files to those actually used
 * in content. This source plugin cross-references the files from contentful
 * to the export of a web spider of the legacy site.
 *
 * This parser also supports filtering by content type. This allows it to be
 * used for separate image/document media migrations purely by migration config.
 *
 * @DataParser(
 *   id = "contentful_files",
 *   title = @Translation("Contentful Files")
 * )
 */
class ContentfulFiles extends Json {

  /**
   * Optional array of mime types to include in this source.
   *
   * @var array
   */
  protected array $contentTypes;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->contentTypes = $configuration['content_types'] ?? [];
  }

  /**
   * {@inheritdoc}
   */
  protected function getSourceData(string $url): array {
    $asset_usage_csv = fopen('private://migration_data/website-asset-use.csv', 'r');
    $used_urls = [];
    while ($row = fgetcsv($asset_usage_csv)) {
      // Ignore the header and totals rows.
      if ($row[0] == 'asset' || $row[0] == 'Grand Total') {
        continue;
      }
      // Some URLs in the usage spreadsheet start with the protocol. We need to
      // strip this to successfully match with those in the JSON export.
      // We convert the URL to lowercase before comparison as technically,
      // protocols are case-insensitive.
      if (str_starts_with(strtolower($row[0]), 'https:')) {
        $row[0] = substr($row[0], strlen('https:'));
      }
      if (str_starts_with(strtolower($row[0]), 'http:')) {
        $row[0] = substr($row[0], strlen('http:'));
      }
      $used_urls[] = $row[0];
    }

    // Filter out everything we don't want from the data sourced from the JSON
    // export.
    return array_filter(parent::getSourceData($url), function ($value) use ($used_urls) {
      // Ignore files with an empty URL. We can't migrate them.
      if (empty($value['fields']['file']['en-GB']['url'])) {
        return FALSE;
      }
      // Ignore files of unsupported type.
      if (!empty($this->contentTypes)) {
        if (!in_array($value['fields']['file']['en-GB']['contentType'], $this->contentTypes)) {
          return FALSE;
        }
      }
      // Include files that are in the usage spreadsheet.
      if (in_array($value['fields']['file']['en-GB']['url'], $used_urls)) {
        return TRUE;
      }
      // If we reach here, the file is in the JSON export but not the spider of
      // the website, so don't include it.
      return FALSE;
    });
  }

}
