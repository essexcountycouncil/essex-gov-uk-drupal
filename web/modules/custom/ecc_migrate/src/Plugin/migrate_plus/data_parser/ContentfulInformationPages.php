<?php

namespace Drupal\ecc_migrate\Plugin\migrate_plus\data_parser;

/**
 * Types of behaviour for restricting the information pages required.
 */
enum sectionOptions {
  case IncludeAll;
  case IncludeWithoutSections;
  case IncludeWithSections;
}

/**
 * JSON Parser for finding Information Pages, aka articles.
 *
 * @DataParser(
 *   id = "contentful_information_pages",
 *   title = @Translation("Contentful Information Pages")
 * )
 */
class ContentfulInformationPages extends JsonContentful {

  /**
   * Options for restricting data set due to the presence of sections.
   */
  protected sectionOptions $sectionOptions = sectionOptions::IncludeAll;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->contentType = 'article';
    if (isset($configuration['with_sections'])) {
      $this->sectionOptions = sectionOptions::IncludeWithSections;
      if (!$configuration['with_sections']) {
        $this->sectionOptions = sectionOptions::IncludeWithoutSections;
      }
    }

  }

  /**
   * {@inheritdoc}
   */
  protected function getSourceData(string $url): array {
    $data = parent::getSourceData($url);
    // If we're including all sections, we already have the data we need.
    if ($this->sectionOptions == sectionOptions::IncludeAll) {
      return $data;
    }
    return array_filter($data, function ($datum) {
      // Otherwise, filter our data depending on whether the source item has
      // sections or not.
      if (empty($datum['fields']['sections'])) {
        return ($this->sectionOptions == sectionOptions::IncludeWithoutSections);
      }
      return ($this->sectionOptions == sectionOptions::IncludeWithSections);
    });
  }

}
