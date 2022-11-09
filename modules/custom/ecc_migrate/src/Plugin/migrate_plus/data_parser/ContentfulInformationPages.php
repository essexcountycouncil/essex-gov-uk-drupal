<?php

namespace Drupal\ecc_migrate\Plugin\migrate_plus\data_parser;

/**
 * Types of behaviour for restricting the information pages required.
 */
enum sectionOptions {
  case IncludeAll;
  case ExcludeWithSections;
  case ExcludeWithoutSections;
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
  protected sectionOptions $sectionOptions;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->contentType = 'article';

    $this->sectionOptions = sectionOptions::IncludeAll;
    if (isset($configuration['with_sections'])) {
      $this->sectionOptions = sectionOptions::ExcludeWithoutSections;
      if ($configuration['with_sections']) {
        $this->sectionOptions = sectionOptions::ExcludeWithSections;
      }
    }

  }

  /**
   * {@inheritdoc}
   */
  protected function getSourceData(string $url): array {
    $data = parent::getSourceData($url);
    if ($this->sectionOptions == sectionOptions::IncludeAll) {
      return $data;
    }
    $data = array_filter($data, function ($node) {
      // @todo Complete this closure.
      return TRUE;
    });
    return $data;
  }

}
