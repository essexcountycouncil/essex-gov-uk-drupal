<?php

namespace Drupal\ecc_migrate\Plugin\migrate\process;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateLookupInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Wa72\HtmlPageDom\HtmlPageCrawler;

/**
 * Maps embedded YouTube videos to remote_video media entity embeds.
 *
 * Assumes the remote_video entity embeds have already been migrated.
 *
 * Usage:
 *
 * @code
 * process:
 *   bar:
 *     plugin: map_youtube_embeds
 *     source: foo
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "map_youtube_embeds"
 * )
 */
class MapYouTubeEmbeds extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Media storage. For loading media.
   */
  protected EntityStorageInterface $mediaStorage;

  /**
   * Constructs a MapYoutubeEmbeds plugin.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\migrate\MigrateLookupInterface $migrateLookup
   *   The migrate lookup service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, protected MigrateLookupInterface $migrateLookup, EntityTypeManagerInterface $entityTypeManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->mediaStorage = $entityTypeManager->getStorage('media');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('migrate.lookup'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $crawler = new HtmlPageCrawler($value);
    $crawler->filter('iframe[src]')->each(function (HtmlPageCrawler $iframe) {
      $src = $iframe->getAttribute('src');
      // @todo this normalisation is the same as the ContentfulYoutubeEmbeds
      // source plugin. We should put this in a service.
      if (str_starts_with(strtolower($src), 'https://www.youtube.com/embed')) {
        // Remove # at the end and anything after.
        if (str_contains($src, '#')) {
          $src = substr($src, 0, strpos($src, '#'));
        }
        // Some have a double slash after the embed. Remove that to normalise
        // the data.
        if (str_starts_with($src, 'https://www.youtube.com/embed//')) {
          $src = 'https://www.youtube.com/embed/' . substr($src, 31);
        }
        // See if we've migrated this source.
        try {
          if ($media_id = $this->migrateLookup->lookup('ecc_youtube_embeds', [$src])[0]['mid']) {
            if ($media = $this->mediaStorage->load($media_id)) {
              // We have! So convert the iFrame to Drupal media.
              $drupal_media = new HtmlPageCrawler('<drupal-media>');
              $drupal_media->setAttribute('data-align', 'center');
              $drupal_media->setAttribute('data-entity-type', 'media');
              $drupal_media->setAttribute('data-entity-uuid', $media->uuid());
              $iframe->replaceWith($drupal_media);
            }
          }
        }
        catch (PluginException | MigrateException $e) {
          // If something went wrong, do nothing and leave the iframe in place.
        }
      }
    });

    return $crawler->saveHTML();
  }

}
