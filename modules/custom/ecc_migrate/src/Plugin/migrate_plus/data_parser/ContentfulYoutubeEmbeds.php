<?php

namespace Drupal\ecc_migrate\Plugin\migrate_plus\data_parser;

use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;
use Wa72\HtmlPageDom\HtmlPageCrawler;

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
    /*
     * We're getting JSON data as the JsonParser would return it. But we need
     * to transform that significantly to return a list of the YouTube embeds.
     */

    // This will get the data for every single content type in the source..
    $json_source_data = parent::getSourceData($url);
    // ..so filter out everything that doesn't have a body field containing at
    // least one YouTube embedded iFrame.
    $json_source_data = array_filter($json_source_data, function ($value) {
      if (!isset($value['fields']['body']['en-GB'])) {
        return FALSE;
      }
      if (str_contains(strtolower($value['fields']['body']['en-GB']), 'https://www.youtube.com/embed/')) {
        return TRUE;
      }
      return FALSE;
    });

    // We now need to change the shape of our source data, as rather have each
    // individual piece of content as a row, we want every individual embed.
    // First extract all the src elements for YouTube embedded iframes.
    $youtube_srcs = [];
    foreach ($json_source_data as $datum) {
      // We're using the HtmlPageCrawler even though the body field is markdown.
      // That's because the YouTube embeds are in embedded HTML within the
      // markdown, and the crawler can still find them.
      $body_crawler = new HtmlPageCrawler($datum['fields']['body']['en-GB']);
      $body_crawler->filter('iframe[src]')->each(function (HtmlPageCrawler $node) use (&$youtube_srcs) {
        $src = $node->getAttribute('src');
        if (str_starts_with(strtolower($src), 'https://www.youtube.com/embed')) {
          // Remove # at the end and anything after.
          if (str_contains($src, '#')) {
            $src = substr($src, 0, strpos($src, '#'));
          }
          $youtube_srcs[] = $src;
        }
      });
    }
    // We don't want to have the same video multiple times.
    $youtube_srcs = array_unique($youtube_srcs);
    // Map the sources to an ID so the migration system can address src as a
    // field.
    $youtube_source_data = [];
    foreach ($youtube_srcs as $src) {
      $youtube_source_data[] = ['id' => $src];
    }
    return $youtube_source_data;
  }

}
