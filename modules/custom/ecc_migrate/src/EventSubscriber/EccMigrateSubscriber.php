<?php

namespace Drupal\ecc_migrate\EventSubscriber;

use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigratePostRowSaveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Essex County Council Migrate event subscriber.
 */
class EccMigrateSubscriber implements EventSubscriberInterface {

  /**
   * Migrate post row save event handler.
   *
   * Used to set the author of a node after it has been saved.
   * This allows us to have separate node and revision IDs, even when there
   * is only one node.
   *
   * @param \Drupal\migrate\Event\MigratePostRowSaveEvent $event
   *   Event being processed.
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
    // @todo inject this service.
    $node = \Drupal\node\Entity\Node::load($event->getDestinationIdValues()[0]);
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
