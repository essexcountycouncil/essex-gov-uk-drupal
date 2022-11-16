<?php

namespace Drupal\ecc_migrate\EventSubscriber;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigratePostRowSaveEvent;
use Drupal\migrate\MigrateLookupInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Essex County Council Migrate event subscriber.
 */
class EccMigrateSubscriber implements EventSubscriberInterface {

  /**
   * Node storage. For loading nodes in post migrate events.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected EntityStorageInterface $nodeStorage;

  /**
   * Redirect storage. For creating redirects from legacy urls.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected EntityStorageInterface $redirectStorage;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Used for obtaining node storage.
   * @param \Drupal\migrate\MigrateLookupInterface $migrateLookup
   *   Migrate lookup service.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, protected MigrateLookupInterface $migrateLookup) {
    $this->nodeStorage = $entityTypeManager->getStorage('node');
    $this->redirectStorage = $entityTypeManager->getStorage('redirect');
  }

  /**
   * Post row save central handler.
   *
   * Calls other functions to actually do things.
   *
   * @param \Drupal\migrate\Event\MigratePostRowSaveEvent $event
   *   Event that happened.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function onPostRowSave(MigratePostRowSaveEvent $event) {
    $this->setAuthors($event);
    $this->createLegacyRedirect($event);
    $this->reorderGuidePages($event);
  }

  /**
   * Sets created/updated authors.
   *
   * Used to set the author of a node after it has been saved.
   * This allows us to have separate node and revision UIDs, even when there
   * is only one node.
   *
   * @param \Drupal\migrate\Event\MigratePostRowSaveEvent $event
   *   Event being processed.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function setAuthors(MigratePostRowSaveEvent $event) {
    // Don't do this for all migrations.
    if (!in_array($event->getMigration()->getPluginId(), [
      'ecc_news',
      'ecc_service_landing_pages',
    ])) {
      return;
    }
    $row = $event->getRow();
    $destination = $row->getDestination();
    if (!isset($destination['uid'])) {
      return;
    }
    if (!isset($destination['pseudo_uid'])) {
      return;
    }
    if ($destination['pseudo_uid'] === $destination['uid']) {
      return;
    }
    $node = $this->nodeStorage->load($event->getDestinationIdValues()[0]);
    $node->uid = $destination['pseudo_uid'];
    $node->save();
  }

  /**
   * Creates a redirect from the legacy url to the newly created entity.
   *
   * @param \Drupal\migrate\Event\MigratePostRowSaveEvent $event
   *   Event being processed.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function createLegacyRedirect(MigratePostRowSaveEvent $event) {
    $legacy_url = $event->getRow()->getDestinationProperty('legacy_url');
    // If we didn't set a legacy url in the migration..
    if (!$legacy_url) {
      // ..then we don't want to create a redirect.
      return;
    }
    $this->redirectStorage->create([
      'redirect_source' => $legacy_url,
      'redirect_redirect' => 'internal:/node/' . $event->getDestinationIdValues()[0],
      'status_code' => '301',
    ])->save();
  }

  /**
   * Reorder the child guide pages of an overview after migration.
   *
   * This is necessary because the order of guide pages contained within a
   * guide overview comes from the Sections field of the ecc_guide_overviews
   * migration. But, because of the way LocalGov Drupal maintains
   * back-references, the guide pages are attributed to guide overviews in the
   * evv_guide_pages migration. This means that the order cannot be set when
   * that relationship was made.
   *
   * Therefore, we have a post-row save event on the ecc_guide_overviews
   * migration, when the guide overview has all its pages assigned, but they
   * are not in the correct order. We then have to compare the guide pages we
   * have with the order specified in the sections field of the
   * ecc_guide_overviews migration, and re-save the node in that order.
   *
   * Note that the source system does not enforce that guide pages with a given
   * parent are reciprocally children of that parent. So the sections field
   * may contain references that are not saved against the row.
   *
   * @param \Drupal\migrate\Event\MigratePostRowSaveEvent $event
   *   Post row save event.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \Drupal\migrate\MigrateException
   */
  public function reorderGuidePages(MigratePostRowSaveEvent $event) {
    if ($event->getMigration()->getPluginId() != 'ecc_guide_overviews') {
      return;
    }
    // Get the current unsorted list of node IDs...
    /** @var \Drupal\node\NodeInterface $guide_overview_node */
    $guide_overview_node = $this->nodeStorage->load($event->getDestinationIdValues()[0]);
    $row = $event->getRow();
    $unsorted_guide_page_nids = [];
    foreach ($guide_overview_node->get('localgov_guides_pages') as $entity_reference) {
      $unsorted_guide_page_nids[] = $entity_reference->target_id;
    }
    if (!$unsorted_guide_page_nids) {
      return;
    }
    // ..then get the sorted list of source IDs from the migration..
    $sorted_guide_page_nids = [];
    foreach ($row->get('sections') as $section) {
      // .. and convert each to its destination ID..
      $section_nid = $this->migrateLookup->lookup(['ecc_guide_pages'], [$section['sys']['id']]);
      $section_nid = reset($section_nid)['nid'];
      // ..and if the destination ID is in the unsorted list..
      if (in_array($section_nid, $unsorted_guide_page_nids)) {
        // ..add it to the sorted list.
        $sorted_guide_page_nids[] = $section_nid;
      }
    }
    // If we actually made a sorted list with anything in it..
    if ($sorted_guide_page_nids) {
      // ..save it against the guide overview page so that all the guide pages
      // ..are listed in the same order as the source system.
      $guide_overview_node->set('localgov_guides_pages', $sorted_guide_page_nids);
      $guide_overview_node->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      MigrateEvents::POST_ROW_SAVE => ['onPostRowSave'],
    ];
  }

}
