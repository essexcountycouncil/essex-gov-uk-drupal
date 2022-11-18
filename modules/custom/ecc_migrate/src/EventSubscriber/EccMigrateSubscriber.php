<?php

namespace Drupal\ecc_migrate\EventSubscriber;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigratePostRowSaveEvent;
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
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
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
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      MigrateEvents::POST_ROW_SAVE => ['onPostRowSave'],
    ];
  }

}
