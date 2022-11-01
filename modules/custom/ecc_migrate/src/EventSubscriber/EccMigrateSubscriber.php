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
  }

  /**
   * Migrate post row save event handler.
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
  public function onPostRowSave(MigratePostRowSaveEvent $event) {
    // Don't do this for all migrations.
    if (!in_array($event->getMigration()->getPluginId(), ['ecc_news'])) {
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
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      MigrateEvents::POST_ROW_SAVE => ['onPostRowSave'],
    ];
  }

}
